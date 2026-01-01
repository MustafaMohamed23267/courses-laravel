<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Courses;
use App\Http\Requests\StorecoursesRequest;
use App\Http\Requests\UpdateCoursesRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Collection;

class CourseServices
{
    /**
     * Create a new course
     *
     * @param StorecoursesRequest $request
     * @return Courses
     */
    public function createCourse(StorecoursesRequest $request): Courses
    {
        $data = $request->validated();
        $data['instructor_id'] = Auth::id();

        if ($request->hasFile('image_url')) {
            $data['image_url'] = $request->file('image_url')
                ->store('courses', 'public');
        }

        return Courses::create($data);
    }

    /**
     * Get a course by ID
     *
     * @param int $id
     * @return Courses|null
     */
    public function getCourse(int $id): ?Courses
    {
    //     $enrolled = Booking::where('user_id', auth()->id())
    // ->where('course_id', $course->id)
    // ->exists();
        return Courses::find($id);
    }

    /**
     * Get all courses
     *
     * @return Collection
     */
    public function getAllCourses(): Collection
    {
           // return Courses::with('category')->get();

        return Courses::all();
    }

    /**
     * Update a course
     *
     * @param int $id
     * @param UpdateCoursesRequest $request
     * @return Courses|null
     */
    public function updateCourse(int $id, UpdateCoursesRequest $request): ?Courses
    {
        $course = $this->getCourse($id);
        if (!$course) {
            return null;
        }

        $data = $request->validated();

        // Handle image upload
        if ($request->hasFile('image_url')) {
            // Delete old image if exists
            if ($course->image_url) {
                Storage::disk('public')->delete($course->image_url);
            }
            $data['image_url'] = $request->file('image_url')->store('courses', 'public');
        }

        $course->update($data);
        return $course->fresh();
    }

    /**
     * Delete a course
     *
     * @param int $id
     * @return bool
     */
    public function deleteCourse(int $id): bool
    {
        $course = $this->getCourse($id);
        if (!$course) {
            return false;
        }

        // Delete course image
        if ($course->image_url) {
            Storage::disk('public')->delete($course->image_url);
        }

        return $course->delete();
    }

    /**
     * Reduce available seats when a booking is made
     *
     * @param int $courseId
     * @param int $quantity
     * @return bool
     */
    public function reduceSeats(int $courseId, int $quantity): bool
    {
        $course = $this->getCourse($courseId);
        if (!$course || $course->available_seats < $quantity) {
            return false;
        }

        $course->available_seats -= $quantity;
        return $course->save();
    }

    /**
     * Increase available seats (e.g., booking cancelled)
     *
     * @param int $courseId
     * @param int $quantity
     * @return bool
     */
    public function increaseSeats(int $courseId, int $quantity): bool
    {
        $course = $this->getCourse($courseId);
        if (!$course) {
            return false;
        }

        $course->available_seats += $quantity;
        // Ensure available_seats does not exceed total_seats
        if ($course->available_seats > $course->total_seats) {
            $course->available_seats = $course->total_seats;
        }

        return $course->save();
    }
}
