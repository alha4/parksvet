<?php
namespace B24Entity;

use B24Entity\IResponse;

class JsonResponse implements IResponse {

  public function parse($entity) {

      return json_encode($entity,JSON_UNESCAPED_UNICODE);

  }
}
