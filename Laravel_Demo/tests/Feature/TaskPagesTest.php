<?php

namespace Tests\Feature;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_about_page_displays_the_course_and_module(): void
    {
        $response = $this->get('/about');

        $response->assertOk()
            ->assertSee('My Task Manager')
            ->assertSee('A practical Laravel project built for learning.')
            ->assertSee('Web System Technologies')
            ->assertSee('Laravel Foundations')
            ->assertSee('From request to page');
    }

    public function test_task_parameter_page_displays_the_given_id(): void
    {
        $response = $this->get('/tasks/15');

        $response->assertOk()
            ->assertSee('My Task Manager')
            ->assertSee('A dynamic task preview.')
            ->assertSee('Buy groceries')
            ->assertSee('Task ID')
            ->assertSee('15');
    }

    public function test_tasks_page_displays_saved_tasks(): void
    {
        Task::create([
            'task_title' => 'Read Laravel tutorial',
            'description' => 'Complete the database section.',
        ]);

        Task::create([
            'task_title' => 'Submit activity',
            'completed' => true,
        ]);

        $response = $this->get(route('tasks.index'));

        $response->assertOk()
            ->assertSee('My Task Manager')
            ->assertSee('Read Laravel tutorial')
            ->assertSee('Complete the database section.')
            ->assertSee('Pending')
            ->assertSee('Completed')
            ->assertSee('2 tasks');
    }

    public function test_a_task_can_be_added_from_the_form(): void
    {
        $response = $this->post(route('tasks.store'), [
            'task_title' => 'Buy groceries',
            'description' => 'Milk and bread',
        ]);

        $response->assertRedirectToRoute('tasks.index')
            ->assertSessionHas('success', 'Task added successfully.');

        $this->assertDatabaseHas('tasks', [
            'task_title' => 'Buy groceries',
            'description' => 'Milk and bread',
            'completed' => false,
        ]);
    }

    public function test_task_title_is_required(): void
    {
        $response = $this->from(route('tasks.index'))->post(route('tasks.store'), [
            'task_title' => '',
            'description' => 'Missing a title',
        ]);

        $response->assertRedirect(route('tasks.index'))
            ->assertSessionHasErrors('task_title');

        $this->assertDatabaseCount('tasks', 0);
    }
}
