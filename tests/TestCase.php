<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use ScottSmith\Doctrine\Integration\Laravel\TestConnectionProviderTrait;
use ScottSmith\Doctrine\Integration\Testing\ConnectionAwareInterface;
use ScottSmith\Doctrine\Integration\Testing\EntityManagerAwareInterface;

abstract class TestCase extends BaseTestCase implements ConnectionAwareInterface, EntityManagerAwareInterface
{
    use CreatesApplication;
    use TestConnectionProviderTrait;
}
