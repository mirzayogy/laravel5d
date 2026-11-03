<?php

namespace Tests\Feature;

use App\Notifications\AppNotification;
use Tests\FlagboardTestCase;

class NotificationTest extends FlagboardTestCase
{
    public function test_the_teammates_hear_about_a_solve_and_a_first_blood(): void
    {
        $competition = $this->makeCompetition();
        $solver = $this->makePlayer();
        $mate = $this->makePlayer($solver->team);
        $this->register($solver->team, $competition);
        $challenge = $this->makeChallenge($competition, 'FLAG{go}');

        $this->actingAs($solver)->post(route('challenges.submit', $challenge), ['flag' => 'FLAG{go}'])->assertSessionHas('success');

        $this->assertSame('First blood!', $mate->notifications()->first()->data['title']);
        $this->assertSame(0, $solver->notifications()->count());
    }

    public function test_a_notification_can_be_opened_and_marked_as_read(): void
    {
        $user = $this->makePlayer();
        $user->notify(new AppNotification('Hello', 'World', '/dashboard'));
        $notification = $user->notifications()->first();

        $this->actingAs($user)->get(route('notifications.index'))->assertOk()->assertSee('Hello');
        $this->actingAs($user)->post(route('notifications.read', $notification->id))->assertRedirect('/dashboard');

        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_all_notifications_can_be_marked_as_read(): void
    {
        $user = $this->makePlayer();
        $user->notify(new AppNotification('One', 'a'));
        $user->notify(new AppNotification('Two', 'b'));

        $this->actingAs($user)->post(route('notifications.read-all'))->assertSessionHas('success');

        $this->assertSame(0, $user->unreadNotifications()->count());
    }

    public function test_the_notifications_of_someone_else_are_not_reachable(): void
    {
        $owner = $this->makePlayer();
        $owner->notify(new AppNotification('Private', 'x'));
        $id = $owner->notifications()->first()->id;

        $this->actingAs($this->makePlayer())->post(route('notifications.read', $id))->assertNotFound();
    }
}
