<?php

namespace LattaAi\Recorder\Laravel;

use Exception;
use Illuminate\Support\Facades\Storage;
use LattaAi\Recorder\LattaAttachment;
use LattaAi\Recorder\LattaRecorder;
use LattaAi\Recorder\LattaUtils;
use LattaAi\Recorder\models\LattaInstance;

class LattaLaravelRecorder extends LattaRecorder {

    public function startRecording($framework, $framework_version, $os, $lang, $device) {
        if(!Storage::exists("latta-instance.txt")) {
            $lattaInstance = $this->api->putInstance($framework, $framework_version, $os, $lang, $device);
            Storage::put("latta-instance.txt", $lattaInstance->getId());
        }

        $header = request()->header("Latta-Recording-Relation-Id");

        LattaRecorder::$relationID = isset($_COOKIE["Latta-Recording-Relation-Id"]) ? $_COOKIE["Latta-Recording-Relation-Id"] : $header;

        if (LattaRecorder::$relationID == null) {
            LattaRecorder::$relationID = LattaUtils::uuidv4();
            setcookie("Latta-Recording-Relation-Id", LattaRecorder::$relationID, time() + (10 * 365 * 24 * 60 * 60), "/");
        }
    }

    public function reportError(Exception $exception) {
        $lattaInstance = new LattaInstance(Storage::get("latta-instance.txt"));
        $lattaSnapshot = $this->api->putSnapshot($lattaInstance, "", null, LattaRecorder::$relationID);

        $attachment = new LattaAttachment($exception, LattaRecorder::$logs);
        $this->api->putAttachment($lattaSnapshot, $attachment);
    }
}