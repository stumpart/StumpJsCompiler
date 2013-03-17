<?php

namespace StumpJsCompiler\Channels;

interface IMinify {
    function minify();
    
    function setMinifiedDirectory();
}
