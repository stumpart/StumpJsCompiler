<?php

namespace StumpJsCompiler\Channels;

interface IMinify {
    function minify();
    
    public function setMinifiedDirectory();
}
