<?php
/**
 * This file is part of the O2System Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author         Steeve Andrian Salim
 * @copyright      Copyright (c) Steeve Andrian Salim
 */

// ------------------------------------------------------------------------

namespace O2System\Framework\Services;

// ------------------------------------------------------------------------

use O2System\Spl\DataStructures\SplArrayQueue;

/**
 * Class Queue
 * @package O2System\Framework\Services
 */
class Queue extends SplArrayQueue
{
    /**
     * Queue::process
     */
    public function process()
    {
        return;
    }

    protected function finish($task)
    {
        // @todo: update database set flag task is done

    }

    public function run()
    {
        while(1) {

            // fetch the job the array pointer is on. if we are
            // at the end of the array we will get nothing back
            $job = current($this);
            $jobkey = key($this);

            if($job) {
                echo 'processing job ', $job, PHP_EOL;

                process($job);

                // push the array pointer to the next job
                next($this);

                // release the completed job to free memory
                $this->offsetUnset($jobkey);
            } else {
                echo 'no jobs to do - waiting...', PHP_EOL;
                sleep(10);
            }

        }
    }
}