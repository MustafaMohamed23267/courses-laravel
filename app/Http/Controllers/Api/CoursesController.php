<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use Exception;
use App\Models\Courses;
use App\Helper\ApiResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;
use App\Services\CourseServices;
use App\Http\Controllers\Controller;
use App\Http\Resources\CoursesResource;
use App\Http\Requests\StorecoursesRequest;
use App\Http\Requests\UpdateCoursesRequest;

#[OA\Tag(
    name: "Courses",
    description: "Endpoints for managing courses"
)]
class CoursesController extends Controller
{
    protected $courseService;

    public function __construct(CourseServices $courseService)
    {
        $this->courseService = $courseService;
    }

    #[OA\Get(
        path: "/api/courses",
        summary: "Get all courses",
        tags: ["Courses"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Courses retrieved successfully",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: "id", type: "integer", example: 1),
                            new OA\Property(property: "title", type: "string", example: "Laravel Basics"),
                            new OA\Property(property: "description", type: "string", example: "Learn Laravel from scratch"),
                            new OA\Property(property: "level", type: "string", example: "Beginner"),
                            new OA\Property(property: "total_seats", type: "integer", example: 20),
                            new OA\Property(property: "available_seats", type: "integer", example: 20),
                            new OA\Property(property: "rating", type: "number", example: 4.5),
                            new OA\Property(property: "duration", type: "string", example: "3 hours"),
                            new OA\Property(property: "category_id", type: "integer", example: 1),
                            new OA\Property(property: "image_url", type: "string", example: "courses/image.jpg")
                        ]
                    )
                )
            ),
            new OA\Response(
                response: 400,
                description: "Request failed",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Request failed")
                    ]
                )
            )
        ]
    )]
    public function index()
    {
       try {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        // ADMIN
       if ($user->role === 'admin') {
        return response()->json([
            'courses' => Courses::with('category', 'instructor')->get(),
            'allcourses' => Courses::with('category', 'instructor')->get(),
        ]);
}

        // INSTRUCTOR
        if ($user->role === 'instructor') {
            return response()->json([
                'courses' => $user->courses()->with('category')->get(),
                'allcourses' => Courses::with('category', 'instructor')->get(),
            ]);
        }

        // STUDENT
        if ($user->role === 'instructor') {
        return response()->json([
            'courses' => $user->enrolledCourses()->with('category')->get(),
            'allcourses' => Courses::with('category', 'instructor')->get(),
        ]);}

         return response()->json([
            'allcourses' => Courses::with('category', 'instructor')->get(),
        ]);

    }
         catch (Exception $e) {
            return ApiResponse::error('Request failed', ['message' => $e->getMessage()], 400);
        }
    }


    #[OA\Get(
        path: "/api/courses/{id}",
        summary: "Get single course",
        tags: ["Courses"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "Course ID",
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Course retrieved successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "title", type: "string", example: "Laravel Basics"),
                        new OA\Property(property: "description", type: "string", example: "Learn Laravel from scratch"),
                        new OA\Property(property: "level", type: "string", example: "Beginner"),
                        new OA\Property(property: "total_seats", type: "integer", example: 20),
                        new OA\Property(property: "available_seats", type: "integer", example: 20),
                        new OA\Property(property: "rating", type: "number", example: 4.5),
                        new OA\Property(property: "duration", type: "string", example: "3 hours"),
                        new OA\Property(property: "category_id", type: "integer", example: 1),
                        new OA\Property(property: "image_url", type: "string", example: "courses/image.jpg")
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Course not found",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Course not found")
                    ]
                )
            )
        ]
    )]
    public function show($id)
    {
        try {
            $course = $this->courseService->getCourse($id);
            if (!$course) {
                return ApiResponse::error('Course not found', [], 404);
            }
            return ApiResponse::success(new CoursesResource($course), 'Course retrieved successfully');
        } catch (Exception $e) {
            return ApiResponse::error('Request failed', ['message' => $e->getMessage()], 400);
        }
    }

    #[OA\Post(
        path: "/api/courses",
        summary: "Create a new course",
        tags: ["Courses"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: "multipart/form-data",
                schema: new OA\Schema(
                    required: ["title", "description", "level", "total_seats", "category_id"],
                    properties: [
                        new OA\Property(property: "title", type: "string", example: "Laravel Basics"),
                        new OA\Property(property: "description", type: "string", example: "Learn Laravel from scratch"),
                        new OA\Property(property: "level", type: "string", example: "Beginner"),
                        new OA\Property(property: "total_seats", type: "integer", example: 20),
                        new OA\Property(property: "available_seats", type: "integer", example: 20),
                        new OA\Property(property: "rating", type: "number", example: 4.5),
                        new OA\Property(property: "duration", type: "string", example: "3 hours"),
                        new OA\Property(property: "category_id", type: "integer", example: 1),
                        new OA\Property(property: "image_url", type: "string", format: "binary", description: "Course image file")
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Course created successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "title", type: "string", example: "Laravel Basics"),
                        new OA\Property(property: "description", type: "string", example: "Learn Laravel from scratch"),
                        new OA\Property(property: "level", type: "string", example: "Beginner"),
                        new OA\Property(property: "total_seats", type: "integer", example: 20),
                        new OA\Property(property: "available_seats", type: "integer", example: 20),
                        new OA\Property(property: "rating", type: "number", example: 4.5),
                        new OA\Property(property: "duration", type: "string", example: "3 hours"),
                        new OA\Property(property: "category_id", type: "integer", example: 1),
                        new OA\Property(property: "image_url", type: "string", example: "courses/image.jpg")
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: "Request failed",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Request failed")
                    ]
                )
            )
        ]
    )]
    public function store(StorecoursesRequest $request)
    {
        try {
            $course = $this->courseService->createCourse($request);
            return ApiResponse::success(new CoursesResource($course), 'Course created successfully', 201);
        } catch (Exception $e) {
            return ApiResponse::error('Request failed', ['message' => $e->getMessage()], 400);
        }
    }

    #[OA\Post(
        path: "/api/courses/{id}",
        summary: "Update a course",
        tags: ["Courses"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "Course ID",
                schema: new OA\Schema(type: "integer")
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: "multipart/form-data",
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(property: "_method", type: "string", example: "PUT", description: "HTTP method override"),
                        new OA\Property(property: "title", type: "string", example: "Laravel Advanced"),
                        new OA\Property(property: "description", type: "string", example: "Advanced Laravel concepts"),
                        new OA\Property(property: "level", type: "string", example: "Intermediate"),
                        new OA\Property(property: "total_seats", type: "integer", example: 25),
                        new OA\Property(property: "available_seats", type: "integer", example: 20),
                        new OA\Property(property: "rating", type: "number", example: 4.7),
                        new OA\Property(property: "duration", type: "string", example: "5 hours"),
                        new OA\Property(property: "category_id", type: "integer", example: 2),
                        new OA\Property(property: "image_url", type: "string", format: "binary", description: "Course image file (optional)")
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Course updated successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "title", type: "string", example: "Laravel Advanced"),
                        new OA\Property(property: "description", type: "string", example: "Advanced Laravel concepts"),
                        new OA\Property(property: "level", type: "string", example: "Intermediate"),
                        new OA\Property(property: "total_seats", type: "integer", example: 25),
                        new OA\Property(property: "available_seats", type: "integer", example: 20),
                        new OA\Property(property: "rating", type: "number", example: 4.7),
                        new OA\Property(property: "duration", type: "string", example: "5 hours"),
                        new OA\Property(property: "category_id", type: "integer", example: 2),
                        new OA\Property(property: "image_url", type: "string", example: "courses/image.jpg")
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Course not found",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Course not found")
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: "Request failed",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Request failed")
                    ]
                )
            )
        ]
    )]
    public function update(UpdateCoursesRequest $request, $id)
    {
        try {
            $course = $this->courseService->updateCourse($id, $request);
            if (!$course) {
                return ApiResponse::error('Course not found', [], 404);
            }
            return ApiResponse::success(new CoursesResource($course), 'Course updated successfully');
        } catch (Exception $e) {
            return ApiResponse::error('Request failed', ['message' => $e->getMessage()], 400);
        }
    }

    #[OA\Delete(
        path: "/api/courses/{id}",
        summary: "Delete a course",
        tags: ["Courses"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "Course ID",
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Course deleted successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Course deleted successfully")
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Course not found",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Course not found")
                    ]
                )
            )
        ]
    )]
    public function destroy($id)
    {
        try {
            $deleted = $this->courseService->deleteCourse($id);
            if (!$deleted) {
                return ApiResponse::error('Course not found', [], 404);
            }
            return ApiResponse::success(null, 'Course deleted successfully');
        } catch (Exception $e) {
            return ApiResponse::error('Request failed', ['message' => $e->getMessage()], 400);
        }
    }
}
