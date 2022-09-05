<?php
    namespace native\libs;

    use Exception;

    class Queue {
        // Class of the Job to run
        private string $job;

        // ISO UTC Date of when the Job should be run only once
        private string $scheduled_for;

        // Human interval defining the rate at which the Job should be run
        // Example : "5 seconds" or even "3 minutes"
        private string $schedule_frequency;

        // ISO UTC Date of when the rate of running the Job can be started
        private string $schedule_from;

        // Whether or not the Job supports multiple instances at the same time or not
        private bool $is_exclusive;

        // The parameters to run the Job with
        private array $context;

        private function __construct(string $job) {
            $this->job = $job;
            $this->is_exclusive = false;
            $this->context = [];
        }

        public static function schedule(Job $job) : Queue {
            return (new Queue(get_class($job)));
        }

        public function exclusive() : Queue {
            $this->is_exclusive = true;
            return $this;
        }

        /**
         * @param {string} $interval An ISO UTC date
         */
        public function for(string $date) : Queue {
            if ( $date === 'now' )
                $date = date('c');

            $this->scheduled_for = $date;
            $this->is_exclusive = true;

            return $this;
        }

        /**
         * @param {string} $interval A human readable duration understood by strtotime
         */
        public function in(string $interval) : Queue {
            $now = time();
            $later = $now + strtotime($interval, 0);

            $this->scheduled_for = date('c', $later);
            $this->is_exclusive = true;

            return $this;
        }

        /**
         * @param {string} $frequency A human readable frequency understood by strtotime
         */
        public function every(string $frequency) : Queue {
            $this->schedule_frequency = $frequency;

            return $this;
        }

        /**
         * @param {string} $interval An ISO UTC date
         */
        public function from(string $date) : Queue {
            if ( $date === 'now' )
                $date = date('c');

            $this->schedule_from = $date;

            return $this;
        }

        public function with(array $context) : Queue {
            $this->context = $context;

            return $this;
        }

        public function persist() : void {
            $service = default_service('jobs');

            $row = [
                'class' => $this->job,
                'context' => json_encode($this->context),
                'is_exclusive' => $this->is_exclusive,
                'is_running' => false,
            ];

            if ( isset($this->schedule_frequency) && isset($this->scheduled_for) )
                throw new Exception("Queue: every() and for() can't be used at the same time. Please use either every()->from() or for() alone. ");


            if ( isset($this->schedule_frequency) )
                $row = array_merge(
                    $row,
                    [
                        'schedule_frequency' => $this->schedule_frequency,
                        'schedule_from' => $this->schedule_from ?? date('c')
                    ]
                );
            elseif ( isset($this->scheduled_for) )
                $row = array_merge(
                    $row,
                    [
                        'scheduled_for' => $this->scheduled_for
                    ]
                );
            else
                throw new Exception('Queue: Please provide proper scheduling settings for the job, using every() / from() / for()');

            $service->create($row);
        }
    }
