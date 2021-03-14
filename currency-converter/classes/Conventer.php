<?php

class Converter {
    public function __construct(
        private string $source_currency=null,
        private string $target_currency=null
      ) {

      }
}