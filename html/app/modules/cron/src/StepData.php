<?php

namespace matpoppl\Cron;

class StepData extends AbstractTaskData implements \JsonSerializable
{
    const STATUS_DONE = 'done';
    const STATUS_ERROR = 'error';
    const STATUS_ERROR_CONTINUE = 'error-continue';
    const STATUS_CONTINUE = 'continue';
    
    public $status = null;
    public $iteration = 0;
    
    public function jsonSerialize()
    {
        return [
            'status' => $this->status,
            'iteration' => $this->iteration,
            'params' => $this->getParams(),
        ];
    }
}
