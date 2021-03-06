<?php

/*
 * This file is part of the broadway/broadway-saga package.
 *
 * (c) Qandidate.com <opensource@qandidate.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require __DIR__ . '/ReservationSaga.php';

use Broadway\CommandHandling\CommandBus;
use Broadway\Saga\SagaInterface;
use Broadway\Saga\Testing\SagaScenarioTestCase;
use Broadway\UuidGenerator\Testing\MockUuidSequenceGenerator;

/**
 * Class ReservationSagaTest'
 * @covers ReservationSaga
 */
class ReservationSagaTest extends SagaScenarioTestCase
{
    /**
     * @param CommandBus $commandBus
     *
     * @return SagaInterface
     */
    protected function createSaga(CommandBus $commandBus): SagaInterface
    {
        return new ReservationSaga($commandBus, new MockUuidSequenceGenerator(
            [
                'bf142ea0-29f7-11e5-9d3f-0002a5d5c51b'
            ]
        ));
    }

    /**
     * @test
     */
    public function it_makes_a_seat_reservation_when_an_order_was_placed(): void
    {
        $this->scenario
            ->when(new OrderPlaced('9d66f760-29f7-11e5-a239-0002a5d5c51b', 5))
            ->then([
                new MakeSeatReservation('bf142ea0-29f7-11e5-9d3f-0002a5d5c51b', 5)
            ]);
    }

    /**
     * @test
     * @covers \Broadway\Saga\Saga
     */
    public function badMethodCallExceptionTest(): void
    {
        $this->expectException(BadMethodCallException::class);
        $this->scenario
            ->when(new BadMethodCall('bf142ea0-29f7-11e5-9d3f-0002a5d5c51b'))
            ->then([]);
    }

    /**
     * @test
     */
    public function it_marks_the_order_as_booked_when_the_seat_reservation_was_accepted(): void
    {
        $this->scenario
            ->given([
                new OrderPlaced('9d66f760-29f7-11e5-a239-0002a5d5c51b', 5)
            ])
            ->when(new ReservationAccepted('bf142ea0-29f7-11e5-9d3f-0002a5d5c51b'))
            ->then([
                new MarkOrderAsBooked('9d66f760-29f7-11e5-a239-0002a5d5c51b')
            ]);
    }

    /**
     * @test
     */
    public function it_rejects_the_order_when_the_seat_reservation_was_rejected(): void
    {
        $this->scenario
            ->given([
                new OrderPlaced('9d66f760-29f7-11e5-a239-0002a5d5c51b', 5)
            ])
            ->when(new ReservationRejected('bf142ea0-29f7-11e5-9d3f-0002a5d5c51b'))
            ->then([
                new RejectOrder('9d66f760-29f7-11e5-a239-0002a5d5c51b')
            ]);
    }
}
