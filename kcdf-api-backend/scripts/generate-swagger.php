<?php

declare(strict_types=1);

$rootDir = dirname(__DIR__);
$config = require $rootDir . '/config/openapi.php';

// Create the OpenAPI specification with all endpoints and request/response bodies
$spec = [
    'openapi' => '3.0.0',
    'info' => [
        'title' => $config['title'],
        'description' => $config['description'],
        'version' => $config['version'],
        'contact' => [
            'name' => $config['contact']['name'],
            'email' => $config['contact']['email'],
        ],
        'license' => [
            'name' => $config['license']['name'],
        ],
    ],
    'servers' => [
        [
            'url' => $config['servers']['development'],
            'description' => 'Development server',
        ],
        [
            'url' => $config['servers']['production'],
            'description' => 'Production server',
        ],
    ],
    'components' => [
        'securitySchemes' => [
            'bearerAuth' => [
                'type' => 'http',
                'scheme' => 'bearer',
                'bearerFormat' => 'JWT',
                'description' => 'JWT Bearer token for authentication',
            ],
        ],
        'schemas' => [
            'SuccessResponse' => [
                'type' => 'object',
                'title' => 'Success Response',
                'properties' => [
                    'success' => ['type' => 'boolean', 'example' => true],
                    'data' => ['type' => 'object'],
                    'message' => ['type' => 'string', 'example' => 'Operation successful'],
                ],
            ],
            'ErrorResponse' => [
                'type' => 'object',
                'title' => 'Error Response',
                'properties' => [
                    'success' => ['type' => 'boolean', 'example' => false],
                    'error' => [
                        'type' => 'object',
                        'properties' => [
                            'code' => ['type' => 'string', 'example' => 'VALIDATION_FAILED'],
                            'message' => ['type' => 'string', 'example' => 'Request validation failed'],
                            'details' => ['type' => 'object'],
                        ],
                    ],
                ],
            ],
            'ValidationErrorResponse' => [
                'type' => 'object',
                'title' => 'Validation Error Response',
                'properties' => [
                    'success' => ['type' => 'boolean', 'example' => false],
                    'error' => [
                        'type' => 'object',
                        'properties' => [
                            'code' => ['type' => 'string', 'example' => 'VALIDATION_FAILED'],
                            'message' => ['type' => 'string', 'example' => 'Validation failed'],
                            'details' => ['type' => 'object'],
                        ],
                    ],
                ],
            ],
            'PaginatedResponse' => [
                'type' => 'object',
                'title' => 'Paginated Response',
                'properties' => [
                    'success' => ['type' => 'boolean', 'example' => true],
                    'data' => ['type' => 'array', 'items' => ['type' => 'object']],
                    'meta' => [
                        'type' => 'object',
                        'properties' => [
                            'total' => ['type' => 'integer', 'example' => 100],
                            'per_page' => ['type' => 'integer', 'example' => 15],
                            'current_page' => ['type' => 'integer', 'example' => 1],
                            'last_page' => ['type' => 'integer', 'example' => 7],
                        ],
                    ],
                ],
            ],
            'LoginRequest' => [
                'type' => 'object',
                'title' => 'Login Request',
                'required' => ['username', 'password'],
                'properties' => [
                    'username' => ['type' => 'string', 'example' => 'john.doe', 'description' => 'User username'],
                    'password' => ['type' => 'string', 'format' => 'password', 'example' => 'secret123', 'description' => 'User password'],
                ],
            ],
            'RefreshTokenRequest' => [
                'type' => 'object',
                'title' => 'Refresh Token Request',
                'required' => ['refresh_token'],
                'properties' => [
                    'refresh_token' => ['type' => 'string', 'example' => 'eyJhbGc...', 'description' => 'Valid refresh token'],
                ],
            ],
            'CreateMemberRequest' => [
                'type' => 'object',
                'title' => 'Create Member Request',
                'required' => ['email', 'first_name', 'last_name'],
                'properties' => [
                    'email' => ['type' => 'string', 'format' => 'email', 'example' => 'member@example.com'],
                    'first_name' => ['type' => 'string', 'example' => 'John'],
                    'last_name' => ['type' => 'string', 'example' => 'Doe'],
                    'phone' => ['type' => 'string', 'example' => '+1234567890'],
                    'date_of_birth' => ['type' => 'string', 'format' => 'date', 'example' => '1990-01-15'],
                ],
            ],
            'UpdateMemberRequest' => [
                'type' => 'object',
                'title' => 'Update Member Request',
                'properties' => [
                    'first_name' => ['type' => 'string', 'example' => 'John'],
                    'last_name' => ['type' => 'string', 'example' => 'Doe'],
                    'phone' => ['type' => 'string', 'example' => '+1234567890'],
                    'date_of_birth' => ['type' => 'string', 'format' => 'date'],
                ],
            ],
            'CreateFamilyRequest' => [
                'type' => 'object',
                'title' => 'Create Family Request',
                'required' => ['name'],
                'properties' => [
                    'name' => ['type' => 'string', 'example' => 'Doe Family'],
                    'description' => ['type' => 'string', 'example' => 'Family description'],
                ],
            ],
            'CreateProgramRequest' => [
                'type' => 'object',
                'title' => 'Create Program Request',
                'required' => ['name', 'description'],
                'properties' => [
                    'name' => ['type' => 'string', 'example' => 'Java Programming'],
                    'description' => ['type' => 'string', 'example' => 'Learn Java basics'],
                    'duration_weeks' => ['type' => 'integer', 'example' => 12],
                    'level' => ['type' => 'string', 'enum' => ['beginner', 'intermediate', 'advanced'], 'example' => 'beginner'],
                ],
            ],
            'CreateBatchRequest' => [
                'type' => 'object',
                'title' => 'Create Batch Request',
                'required' => ['program_id', 'name', 'start_date'],
                'properties' => [
                    'program_id' => ['type' => 'integer', 'example' => 1],
                    'name' => ['type' => 'string', 'example' => 'Batch 1'],
                    'start_date' => ['type' => 'string', 'format' => 'date', 'example' => '2024-07-15'],
                    'end_date' => ['type' => 'string', 'format' => 'date', 'example' => '2024-09-30'],
                    'max_students' => ['type' => 'integer', 'example' => 30],
                ],
            ],
            'CreatePaymentRequest' => [
                'type' => 'object',
                'title' => 'Create Payment Request',
                'required' => ['family_id', 'amount', 'payment_date'],
                'properties' => [
                    'family_id' => ['type' => 'integer', 'example' => 1],
                    'amount' => ['type' => 'number', 'format' => 'decimal', 'example' => 5000],
                    'payment_date' => ['type' => 'string', 'format' => 'date', 'example' => '2024-07-10'],
                    'payment_method' => ['type' => 'string', 'enum' => ['cash', 'check', 'transfer'], 'example' => 'transfer'],
                    'reference_number' => ['type' => 'string', 'example' => 'TRF123456'],
                ],
            ],
            'CreateGroupRequest' => [
                'type' => 'object',
                'title' => 'Create Group Request',
                'required' => ['name'],
                'properties' => [
                    'name' => ['type' => 'string', 'example' => 'Study Group'],
                    'description' => ['type' => 'string', 'example' => 'Group for peer learning'],
                    'category' => ['type' => 'string', 'example' => 'academic'],
                ],
            ],
            'CreateInvitationRequest' => [
                'type' => 'object',
                'title' => 'Create Invitation Request',
                'properties' => [
                    'invite_mobile' => ['type' => 'string', 'example' => '9876543210', 'description' => 'Mobile number to invite'],
                    'invite_email' => ['type' => 'string', 'format' => 'email', 'example' => 'john@example.com', 'description' => 'Email to invite'],
                ],
            ],
            'AcceptInvitationRequest' => [
                'type' => 'object',
                'title' => 'Accept Invitation Request',
                'required' => ['first_name', 'last_name', 'mobile', 'email', 'password'],
                'properties' => [
                    'first_name' => ['type' => 'string', 'example' => 'John', 'description' => 'First name'],
                    'last_name' => ['type' => 'string', 'example' => 'Doe', 'description' => 'Last name'],
                    'mobile' => ['type' => 'string', 'example' => '9876543210', 'description' => 'Mobile number'],
                    'email' => ['type' => 'string', 'format' => 'email', 'example' => 'john@example.com', 'description' => 'Email address'],
                    'password' => ['type' => 'string', 'format' => 'password', 'example' => 'SecurePass123', 'description' => 'Password (min 8 chars)'],
                ],
            ],
            'TokenResponse' => [
                'type' => 'object',
                'title' => 'Token Response',
                'properties' => [
                    'access_token' => ['type' => 'string', 'description' => 'JWT access token'],
                    'refresh_token' => ['type' => 'string', 'description' => 'JWT refresh token'],
                    'token_type' => ['type' => 'string', 'example' => 'Bearer'],
                    'expires_in' => ['type' => 'integer', 'example' => 900],
                ],
            ],
            'SendNotificationRequest' => [
                'type' => 'object',
                'title' => 'Send Notification Request',
                'required' => ['member_ids', 'title', 'message', 'type'],
                'properties' => [
                    'member_ids' => ['type' => 'array', 'items' => ['type' => 'integer'], 'example' => [1, 2, 3]],
                    'title' => ['type' => 'string', 'example' => 'Announcement'],
                    'message' => ['type' => 'string', 'example' => 'Important update for all parents'],
                    'type' => ['type' => 'string', 'enum' => ['in_app', 'push', 'email'], 'example' => 'in_app'],
                ],
            ],
        ],
    ],
    'paths' => [
        '/api/v1/auth/login' => [
            'post' => [
                'operationId' => 'authLogin',
                'tags' => ['Auth'],
                'summary' => 'User login',
                'description' => 'Authenticate user with username and password',
                'requestBody' => [
                    'required' => true,
                    'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/LoginRequest']]],
                ],
                'responses' => [
                    '200' => ['description' => 'Login successful', 'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/SuccessResponse']]]],
                    '401' => ['description' => 'Invalid credentials'],
                    '422' => ['description' => 'Validation failed'],
                ],
            ],
        ],
        '/api/v1/auth/refresh' => [
            'post' => [
                'operationId' => 'authRefresh',
                'tags' => ['Auth'],
                'summary' => 'Refresh access token',
                'requestBody' => [
                    'required' => true,
                    'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/RefreshTokenRequest']]],
                ],
                'responses' => [
                    '200' => ['description' => 'Token refreshed'],
                    '401' => ['description' => 'Invalid refresh token'],
                ],
            ],
        ],
        '/api/v1/auth/logout' => [
            'post' => [
                'operationId' => 'authLogout',
                'tags' => ['Auth'],
                'summary' => 'User logout',
                'security' => [['bearerAuth' => []]],
                'responses' => ['200' => ['description' => 'Logged out']],
            ],
        ],
        '/api/v1/auth/me' => [
            'get' => [
                'operationId' => 'getCurrentProfile',
                'tags' => ['Auth'],
                'summary' => 'Get current user profile',
                'security' => [['bearerAuth' => []]],
                'responses' => ['200' => ['description' => 'Profile retrieved']],
            ],
        ],
        '/api/v1/members' => [
            'get' => [
                'operationId' => 'listMembers',
                'tags' => ['Members'],
                'summary' => 'List member profiles',
                'security' => [['bearerAuth' => []]],
                'parameters' => [
                    ['name' => 'page', 'in' => 'query', 'schema' => ['type' => 'integer', 'example' => 1]],
                    ['name' => 'per_page', 'in' => 'query', 'schema' => ['type' => 'integer', 'example' => 15]],
                ],
                'responses' => ['200' => ['description' => 'Members retrieved', 'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/PaginatedResponse']]]]],
            ],
            'post' => [
                'operationId' => 'createMember',
                'tags' => ['Members'],
                'summary' => 'Create member profile',
                'security' => [['bearerAuth' => []]],
                'requestBody' => [
                    'required' => true,
                    'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/CreateMemberRequest']]],
                ],
                'responses' => ['201' => ['description' => 'Member created']],
            ],
        ],
        '/api/v1/members/{id}' => [
            'get' => [
                'operationId' => 'getMember',
                'tags' => ['Members'],
                'summary' => 'Get member profile',
                'security' => [['bearerAuth' => []]],
                'parameters' => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']]],
                'responses' => ['200' => ['description' => 'Member retrieved']],
            ],
            'put' => [
                'operationId' => 'updateMember',
                'tags' => ['Members'],
                'summary' => 'Update member profile',
                'security' => [['bearerAuth' => []]],
                'parameters' => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']]],
                'requestBody' => [
                    'required' => true,
                    'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/UpdateMemberRequest']]],
                ],
                'responses' => ['200' => ['description' => 'Member updated']],
            ],
        ],
        '/api/v1/families' => [
            'get' => [
                'operationId' => 'listFamilies',
                'tags' => ['Families'],
                'summary' => 'List families',
                'security' => [['bearerAuth' => []]],
                'parameters' => [
                    ['name' => 'page', 'in' => 'query', 'schema' => ['type' => 'integer']],
                    ['name' => 'per_page', 'in' => 'query', 'schema' => ['type' => 'integer']],
                ],
                'responses' => ['200' => ['description' => 'Families retrieved']],
            ],
            'post' => [
                'operationId' => 'createFamily',
                'tags' => ['Families'],
                'summary' => 'Create family',
                'security' => [['bearerAuth' => []]],
                'requestBody' => [
                    'required' => true,
                    'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/CreateFamilyRequest']]],
                ],
                'responses' => ['201' => ['description' => 'Family created']],
            ],
        ],
        '/api/v1/families/{id}' => [
            'get' => [
                'operationId' => 'getFamily',
                'tags' => ['Families'],
                'summary' => 'Get family',
                'security' => [['bearerAuth' => []]],
                'parameters' => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']]],
                'responses' => ['200' => ['description' => 'Family retrieved']],
            ],
            'put' => [
                'operationId' => 'updateFamily',
                'tags' => ['Families'],
                'summary' => 'Update family',
                'security' => [['bearerAuth' => []]],
                'parameters' => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']]],
                'requestBody' => [
                    'required' => true,
                    'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/CreateFamilyRequest']]],
                ],
                'responses' => ['200' => ['description' => 'Family updated']],
            ],
        ],
        '/api/v1/programs' => [
            'get' => [
                'operationId' => 'listPrograms',
                'tags' => ['Programs'],
                'summary' => 'List programs',
                'security' => [['bearerAuth' => []]],
                'responses' => ['200' => ['description' => 'Programs retrieved']],
            ],
            'post' => [
                'operationId' => 'createProgram',
                'tags' => ['Programs'],
                'summary' => 'Create program',
                'security' => [['bearerAuth' => []]],
                'requestBody' => [
                    'required' => true,
                    'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/CreateProgramRequest']]],
                ],
                'responses' => ['201' => ['description' => 'Program created']],
            ],
        ],
        '/api/v1/batches' => [
            'get' => [
                'operationId' => 'listBatches',
                'tags' => ['Batches'],
                'summary' => 'List batches',
                'security' => [['bearerAuth' => []]],
                'responses' => ['200' => ['description' => 'Batches retrieved']],
            ],
            'post' => [
                'operationId' => 'createBatch',
                'tags' => ['Batches'],
                'summary' => 'Create batch',
                'security' => [['bearerAuth' => []]],
                'requestBody' => [
                    'required' => true,
                    'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/CreateBatchRequest']]],
                ],
                'responses' => ['201' => ['description' => 'Batch created']],
            ],
        ],
        '/api/v1/payments' => [
            'get' => [
                'operationId' => 'listPayments',
                'tags' => ['Payments'],
                'summary' => 'List payments',
                'security' => [['bearerAuth' => []]],
                'responses' => ['200' => ['description' => 'Payments retrieved']],
            ],
            'post' => [
                'operationId' => 'createPayment',
                'tags' => ['Payments'],
                'summary' => 'Record payment',
                'security' => [['bearerAuth' => []]],
                'requestBody' => [
                    'required' => true,
                    'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/CreatePaymentRequest']]],
                ],
                'responses' => ['201' => ['description' => 'Payment recorded']],
            ],
        ],
        '/api/v1/groups' => [
            'get' => [
                'operationId' => 'listGroups',
                'tags' => ['Groups'],
                'summary' => 'List groups',
                'security' => [['bearerAuth' => []]],
                'parameters' => [
                    ['name' => 'page', 'in' => 'query', 'schema' => ['type' => 'integer', 'example' => 1]],
                    ['name' => 'per_page', 'in' => 'query', 'schema' => ['type' => 'integer', 'example' => 15]],
                ],
                'responses' => ['200' => ['description' => 'Groups retrieved', 'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/PaginatedResponse']]]]],
            ],
            'post' => [
                'operationId' => 'createGroup',
                'tags' => ['Groups'],
                'summary' => 'Create group',
                'security' => [['bearerAuth' => []]],
                'requestBody' => [
                    'required' => true,
                    'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/CreateGroupRequest']]],
                ],
                'responses' => ['201' => ['description' => 'Group created']],
            ],
        ],
        '/api/v1/groups/{id}' => [
            'get' => [
                'operationId' => 'getGroup',
                'tags' => ['Groups'],
                'summary' => 'Get group details',
                'security' => [['bearerAuth' => []]],
                'parameters' => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']]],
                'responses' => ['200' => ['description' => 'Group retrieved'], '404' => ['description' => 'Group not found']],
            ],
            'put' => [
                'operationId' => 'updateGroup',
                'tags' => ['Groups'],
                'summary' => 'Update group',
                'security' => [['bearerAuth' => []]],
                'parameters' => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']]],
                'requestBody' => [
                    'required' => true,
                    'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/CreateGroupRequest']]],
                ],
                'responses' => ['200' => ['description' => 'Group updated'], '404' => ['description' => 'Group not found']],
            ],
        ],
        '/api/v1/groups/{id}/members' => [
            'get' => [
                'operationId' => 'getGroupMembers',
                'tags' => ['Groups'],
                'summary' => 'Get group members',
                'security' => [['bearerAuth' => []]],
                'parameters' => [
                    ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']],
                    ['name' => 'page', 'in' => 'query', 'schema' => ['type' => 'integer']],
                    ['name' => 'per_page', 'in' => 'query', 'schema' => ['type' => 'integer']],
                ],
                'responses' => ['200' => ['description' => 'Group members retrieved']],
            ],
        ],
        '/api/v1/groups/{id}/join' => [
            'post' => [
                'operationId' => 'joinGroup',
                'tags' => ['Groups'],
                'summary' => 'Join a group',
                'security' => [['bearerAuth' => []]],
                'parameters' => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']]],
                'responses' => ['200' => ['description' => 'Joined group successfully'], '400' => ['description' => 'Cannot join group']],
            ],
        ],
        '/api/v1/groups/{id}/leave' => [
            'delete' => [
                'operationId' => 'leaveGroup',
                'tags' => ['Groups'],
                'summary' => 'Leave a group',
                'security' => [['bearerAuth' => []]],
                'parameters' => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']]],
                'responses' => ['200' => ['description' => 'Left group successfully']],
            ],
        ],
        '/api/v1/groups/{id}/members/{member_id}' => [
            'delete' => [
                'operationId' => 'removeGroupMember',
                'tags' => ['Groups'],
                'summary' => 'Remove member from group',
                'security' => [['bearerAuth' => []]],
                'parameters' => [
                    ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']],
                    ['name' => 'member_id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']],
                ],
                'responses' => ['200' => ['description' => 'Member removed']],
            ],
        ],
        '/api/v1/invitations' => [
            'get' => [
                'operationId' => 'listInvitations',
                'tags' => ['Invitations'],
                'summary' => 'List invitations',
                'security' => [['bearerAuth' => []]],
                'parameters' => [
                    ['name' => 'page', 'in' => 'query', 'schema' => ['type' => 'integer', 'example' => 1]],
                    ['name' => 'per_page', 'in' => 'query', 'schema' => ['type' => 'integer', 'example' => 15]],
                ],
                'responses' => ['200' => ['description' => 'Invitations retrieved', 'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/PaginatedResponse']]]]],
            ],
            'post' => [
                'operationId' => 'createInvitation',
                'tags' => ['Invitations'],
                'summary' => 'Send invitation',
                'security' => [['bearerAuth' => []]],
                'requestBody' => [
                    'required' => true,
                    'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/CreateInvitationRequest']]],
                ],
                'responses' => ['201' => ['description' => 'Invitation sent'], '409' => ['description' => 'Invitation already exists']],
            ],
        ],
        '/api/v1/invitations/{code}' => [
            'get' => [
                'operationId' => 'getInvitationByCode',
                'tags' => ['Invitations'],
                'summary' => 'Get invitation by code',
                'description' => 'Retrieve invitation details using an invitation code (public endpoint)',
                'parameters' => [['name' => 'code', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'string']]],
                'responses' => ['200' => ['description' => 'Invitation retrieved'], '404' => ['description' => 'Invitation not found']],
            ],
        ],
        '/api/v1/invitations/{code}/accept' => [
            'post' => [
                'operationId' => 'acceptInvitation',
                'tags' => ['Invitations'],
                'summary' => 'Accept invitation and create account',
                'description' => 'Register a new user by accepting an invitation (public endpoint)',
                'parameters' => [['name' => 'code', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'string']]],
                'requestBody' => [
                    'required' => true,
                    'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/AcceptInvitationRequest']]],
                ],
                'responses' => [
                    '200' => ['description' => 'Invitation accepted. Account created successfully.', 'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/SuccessResponse']]]],
                    '404' => ['description' => 'Invitation not found'],
                    '409' => ['description' => 'Account already exists'],
                    '422' => ['description' => 'Validation failed or invitation expired'],
                ],
            ],
        ],
        '/api/v1/invitations/{id}' => [
            'delete' => [
                'operationId' => 'cancelInvitation',
                'tags' => ['Invitations'],
                'summary' => 'Cancel invitation',
                'security' => [['bearerAuth' => []]],
                'parameters' => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']]],
                'responses' => ['200' => ['description' => 'Invitation cancelled'], '404' => ['description' => 'Invitation not found']],
            ],
        ],
        '/api/v1/notifications' => [
            'get' => [
                'operationId' => 'listNotifications',
                'tags' => ['Notifications'],
                'summary' => 'List notifications',
                'security' => [['bearerAuth' => []]],
                'parameters' => [
                    ['name' => 'page', 'in' => 'query', 'schema' => ['type' => 'integer']],
                    ['name' => 'per_page', 'in' => 'query', 'schema' => ['type' => 'integer']],
                    ['name' => 'status', 'in' => 'query', 'schema' => ['type' => 'string', 'enum' => ['unread', 'read', 'archived']]],
                ],
                'responses' => ['200' => ['description' => 'Notifications retrieved']],
            ],
            'post' => [
                'operationId' => 'sendNotification',
                'tags' => ['Notifications'],
                'summary' => 'Send notification',
                'security' => [['bearerAuth' => []]],
                'requestBody' => [
                    'required' => true,
                    'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/SendNotificationRequest']]],
                ],
                'responses' => ['201' => ['description' => 'Notification sent']],
            ],
        ],
        '/api/v1/notifications/{id}/read' => [
            'patch' => [
                'operationId' => 'markNotificationAsRead',
                'tags' => ['Notifications'],
                'summary' => 'Mark notification as read',
                'security' => [['bearerAuth' => []]],
                'parameters' => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']]],
                'responses' => ['200' => ['description' => 'Marked as read']],
            ],
        ],
        '/api/v1/notifications/read-all' => [
            'post' => [
                'operationId' => 'markAllNotificationsAsRead',
                'tags' => ['Notifications'],
                'summary' => 'Mark all notifications as read',
                'security' => [['bearerAuth' => []]],
                'responses' => ['200' => ['description' => 'All marked as read']],
            ],
        ],
        '/api/v1/notifications/{id}/archive' => [
            'patch' => [
                'operationId' => 'archiveNotification',
                'tags' => ['Notifications'],
                'summary' => 'Archive notification',
                'security' => [['bearerAuth' => []]],
                'parameters' => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']]],
                'responses' => ['200' => ['description' => 'Notification archived']],
            ],
        ],
        '/api/v1/activity-logs' => [
            'get' => [
                'operationId' => 'listActivityLogs',
                'tags' => ['Activity Logs'],
                'summary' => 'List activity logs',
                'security' => [['bearerAuth' => []]],
                'parameters' => [
                    ['name' => 'page', 'in' => 'query', 'schema' => ['type' => 'integer']],
                    ['name' => 'per_page', 'in' => 'query', 'schema' => ['type' => 'integer']],
                    ['name' => 'actor_profile_id', 'in' => 'query', 'schema' => ['type' => 'integer']],
                    ['name' => 'entity_type', 'in' => 'query', 'schema' => ['type' => 'string']],
                    ['name' => 'action', 'in' => 'query', 'schema' => ['type' => 'string']],
                ],
                'responses' => ['200' => ['description' => 'Activity logs retrieved']],
            ],
        ],
    ],
];

// Create output directory
$outputDir = $rootDir . '/public/swagger';
if (!is_dir($outputDir)) {
    mkdir($outputDir, 0755, true);
}

$outputFile = $outputDir . '/swagger.json';
file_put_contents($outputFile, json_encode($spec, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

$pathCount = count($spec['paths']);
$schemaCount = count($spec['components']['schemas']);
echo "✓ Swagger JSON generated successfully!\n";
echo "  OpenAPI Version: " . $spec['openapi'] . "\n";
echo "  API Title: " . $spec['info']['title'] . "\n";
echo "  API Version: " . $spec['info']['version'] . "\n";
echo "  Endpoints: " . $pathCount . " paths\n";
echo "  Schemas: " . $schemaCount . " defined request/response models\n";
echo "  File: {$outputFile} (" . round(filesize($outputFile) / 1024, 1) . " KB)\n";
