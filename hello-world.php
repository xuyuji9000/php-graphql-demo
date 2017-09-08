<?php

require_once __DIR__.'/vendor/autoload.php';

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use GraphQL\GraphQL;

try {

    // types
    $queryType = new ObjectType([
        'name' => 'Query',
        'fields' => [
            'echo' => [
                'type'  => Type::string(),
                'args'  => [
                    'message' => [
                        'type' => Type::string()
                    ]
                ],
                'resolve' => function($root, $args) {
                    return $root['prefix'] . $args['message'];
                }
            ]
        ]
    ]);

    $mutationType = new ObjectType([
        'name' => 'Calc',
        'fields' => [
            'sum' => [
                'type' => Type::int(),
                'args' => [
                    'x' => [
                        'type' => Type::int()
                    ],
                    'y' => [
                        'type' => Type::int()
                    ]
                ],
                'resolve' => function($root, $args) {
                    return $args['x'] + $args['y'];
                }
            ]
        ]
    ]);

    // schema
    $schema = new Schema([
        'query' => $queryType,
        'mutation' => $mutationType
    ]);

    $rawInput = file_get_contents('php://input');
    $input = json_decode($rawInput, true);
    $query = $input['query'];
    $variableValues = isset($input['variables']) ? $input['variables'] : null;

    $rootValue = ['prefix' => 'You said:'];
    $result = GraphQL::executeQuery($schema, $query, $rootValue, null, $variableValues);

    $output = $result->toArray();

} catch(\Exception $e) {
    $output = [
        'error' => [
            'message' => $e->getMessage()
        ]
    ];
}

header('Content-Type: application/json; charset=UTF-8');
echo json_encode($output);
