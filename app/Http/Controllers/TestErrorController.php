<?php

namespace App\Http\Controllers;

class TestErrorController extends Controller
{
    /**
     * Test 500 Internal Server Error
     */
    public function test500()
    {
        // Log the error for demonstration
        logger()->error('Test 500 error triggered', ['userId' => auth()->id() ?? 1]);

        // Return the 500 error view directly for testing
        // In production, this would be triggered by actual exceptions
        return response()->view('errors.500', [
            'exception' => new \Exception('This is a test 500 error for demonstration purposes.'),
        ], 500);
    }

    /**
     * Test 403 Forbidden Error
     */
    public function test403()
    {
        // This will show the 403 error page
        abort(403, 'This is a test 403 error - access denied.');
    }

    /**
     * Test 404 Not Found Error
     */
    public function test404()
    {
        // This will show the 404 error page
        abort(404, 'This is a test 404 error - page not found.');
    }

    /**
     * Test 419 Page Expired Error (CSRF)
     */
    public function test419()
    {
        // This will show the 419 error page
        abort(419, 'This is a test 419 error - session expired.');
    }
}
