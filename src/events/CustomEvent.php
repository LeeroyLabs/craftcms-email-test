<?php

namespace leeroy\leeroyemailtest\events;

use craft\events\CancelableEvent;

class CustomEvent extends CancelableEvent
{
    public array $templateData;
}