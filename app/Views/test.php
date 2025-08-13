<?php

/**
 * Defines metadata for the database tables, including foreign key relationships.
 * This array can be used to programmatically understand the database schema.
 *
 * @var array<string, array{tablename: string, displayname: string, fields: array<array{name: string, type: string, label: string, editable?: bool, isEnum?: bool, enumValues?: string[], foreignKey?: array{relatedTable: string, displayField: string, valueField: string}}>}>
 */
// Assuming helper('tables_metadata') and getTableMetadata() are defined elsewhere.

helper('tables_metadata');

$tableMetadata = getTableMetadata();

/**
 * Builds the graph from the metadata.
 *
 * @param array $metadata The table metadata.
 * @return array The adjacency list representation of the graph, including foreign key fields.
 */
function buildGraph(array $metadata): array
{
    $graph = [];
    foreach ($metadata as $tableName => $table) {
        if (!isset($graph[$tableName])) {
            $graph[$tableName] = [];
        }
        foreach ($table['fields'] as $field) {
            if (isset($field['foreignKey'])) {
                $relatedTable = $field['foreignKey']['relatedTable'];
                $foreignKeyField = $field['name'];
                // Add a directed edge with the foreign key field
                $graph[$tableName][] = [
                    'neighbor' => $relatedTable,
                    'fk_field' => $foreignKeyField
                ];
                // Ensure the related table is in the graph
                if (!isset($graph[$relatedTable])) {
                    $graph[$relatedTable] = [];
                }
            }
        }
    }
    return $graph;
}


/**
 * Finds all paths between two nodes using Depth-First Search (DFS), recording the join fields.
 *
 * @param array $graph The graph in adjacency list format.
 * @param string $startNode The starting table.
 * @param string $endNode The ending table.
 * @param array $visited Array to track visited nodes in the current path.
 * @param array $path The current path being explored.
 * @param array $allPaths All found paths.
 * @return void
 */
function findAllPaths(array $graph, string $startNode, string $endNode, array &$visited, array &$path, array &$allPaths): void
{
    $visited[$startNode] = true;
    $path[] = ['table' => $startNode];

    if ($startNode === $endNode) {
        $allPaths[] = $path;
    } else {
        if (isset($graph[$startNode])) {
            foreach ($graph[$startNode] as $edge) {
                $neighbor = $edge['neighbor'];
                $fk_field = $edge['fk_field'];
                if (!isset($visited[$neighbor])) {
                    $newPath = $path;
                    $newPath[count($newPath) - 1]['fk_field'] = $fk_field;
                    findAllPaths($graph, $neighbor, $endNode, $visited, $newPath, $allPaths);
                }
            }
        }
    }

    array_pop($path);
    unset($visited[$startNode]);
}

/**
 * Generates an SQL JOIN query for a given path of tables.
 *
 * @param array $path The path of tables and join fields.
 * @return string The generated SQL query.
 */
function generateSqlQuery(array $path): string
{
    $selectClause = "SELECT *";
    $fromClause = "FROM {$path[0]['table']}";
    $joinClauses = [];

    for ($i = 0; $i < count($path) - 1; $i++) {
        $currentTable = $path[$i]['table'];
        $nextTable = $path[$i + 1]['table'];
        $fkField = $path[$i]['fk_field'];
        $joinClauses[] = "INNER JOIN {$nextTable} ON {$currentTable}.{$fkField} = {$nextTable}.id";
    }

    return "{$selectClause} {$fromClause} " . implode(" ", $joinClauses) . ";";
}

// --- Main logic to generate JSON output ---

// Set the header to return JSON
header('Content-Type: application/json');

$graph = buildGraph($tableMetadata);
$allTableNames = array_keys($tableMetadata);
$finalResult = [];

foreach ($allTableNames as $startTable) {
    foreach ($allTableNames as $endTable) {
        if ($startTable !== $endTable) {
            $allPaths = [];
            $visited = [];
            $path = [];
            findAllPaths($graph, $startTable, $endTable, $visited, $path, $allPaths);

            if (!empty($allPaths)) {
                $pathsForPair = [];
                foreach ($allPaths as $currentPath) {
                    $joinConditions = [];
                    for ($i = 0; $i < count($currentPath) - 1; $i++) {
                        $joinConditions[] = [
                            'source_table' => $currentPath[$i]['table'],
                            'source_field' => $currentPath[$i]['fk_field'],
                            'destination_table' => $currentPath[$i + 1]['table'],
                            'destination_field' => 'id' // Assuming foreign keys join to the 'id' field
                        ];
                    }

                    $pathString = implode(' -> ', array_map(function($tableInfo) {
                        return $tableInfo['table'] . (isset($tableInfo['fk_field']) ? " via '{$tableInfo['fk_field']}'" : "");
                    }, $currentPath));

                    $pathsForPair[] = [
                        'path' => $pathString,
                        'join_conditions' => $joinConditions,
                        'sql_query' => generateSqlQuery($currentPath)
                    ];
                }
                
                $finalResult[] = [
                    'sourcetable' => $startTable,
                    'finaltable' => $endTable,
                    'paths' => $pathsForPair
                ];
            }
        }
    }
}

// Return the JSON data
echo json_encode($finalResult, JSON_PRETTY_PRINT);
?>
