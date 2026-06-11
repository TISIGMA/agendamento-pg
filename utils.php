<?php

    function errorAlert($msg){
        alert('alert-danger', $msg);
    }

    function successAlert($msg){
        alert('alert-success', $msg);
    }

    function warningAlert($msg){
      alert('alert-warning', $msg);
    }

    function warningNotification($msg){
      notification('alert-warning notification', $msg);
    }

    function alert($type, $msg){
      echo '<div class="alert '.$type.' alert-dismissible fade show" role="alert">
        '.$msg.'<button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>';
    }

    function notification($type, $msg){
      echo '<div class="alert '.$type.' alert-dismissible fade show" role="alert">
      '.$msg.'<button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>';
    }
?>