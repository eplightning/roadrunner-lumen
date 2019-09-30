<?php

namespace Symfony\Component\HttpFoundation\File {

    /**
     * @see https://github.com/spiral/roadrunner/issues/103
     */
    function is_uploaded_file($filename)
    {
        return true;
    }
}
