<?php

namespace Duochrome\Sismo;

use Symfony\Component\Process\Process;
use Sismo\Notifier;
use Sismo\Commit;

class HoustonNotifier extends Notifier
{
    const SOUND_DEFAULT = 'houston.wav';

    private $soundFail;
    private $soundSuccess;
    private $volume;

    public function __construct($soundFail = 'houston.wav', $soundSuccess = null, $volume = 1)
    {
        $this->soundFail    = (string) $soundFail;
        $this->soundSuccess = (string) $soundSuccess;
        $this->volume       = (float)  $volume;

        if (self::SOUND_DEFAULT === $this->soundFail) {
            $this->soundFail = __DIR__ . '/../../../data/houston.wav';
        }

        if ($this->volume > 1) {
            $this->volume = 1;
        }

        if ($this->volume < 0) {
            $this->volume = 0;
        }
    }

    public function notify(Commit $commit)
    {
        if (!$commit->isSuccessful() && null !== $this->soundFail) {
            $this->play($this->soundFail);
        } elseif ($commit->isSuccessful() && null !== $this->soundSuccess) {
            $this->play($this->soundSuccess);
        }
    }

    protected function play($file)
    {
        if (!file_exists($file)) {
            throw new \InvalidArgumentException(sprintf('Soundfile %s could not be found.'), $file);
        }

        $p = new Process(sprintf('afplay "%s" --volume %s', $file, $this->volume));
        $p->setTimeout(30);

        $p->run();
    }
}
