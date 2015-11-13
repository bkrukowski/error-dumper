<?php

namespace ErrorDumper;

interface StepGeneratorInterface
{
    /**
     * @param array $rawStep
     * @return array
     */
    public function prepareStep(array $rawStep);
}