<?php

namespace App\Job;

use App\Service\Job;

class ConvertDocJob
{
    public function __construct(private \App\Service\Job $executor) {}

    public function execute(array $data)
    {
        $this->executor->execute($data, Job::KEY_CONVERTER);
    }
}