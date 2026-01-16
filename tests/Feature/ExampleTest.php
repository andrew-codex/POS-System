<?php

test('the application returns a successful response', function () {
    // The root redirects to the login page; assert redirect instead of rendering login view
    $response = $this->get('/');

    $response->assertStatus(302);
    $response->assertRedirect('/auth/login');
});
