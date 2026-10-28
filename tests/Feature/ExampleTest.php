<?php

test('halaman login dapat diakses tanpa autentikasi', function () {
    $this->withoutVite();

    $response = $this->get('/login');

    $response->assertOk();
});
