<?php

namespace Duochrome\Sismo;

use Symfony\Component\Process\Process;
use Sismo\Notifier;
use Sismo\Commit;

class HoustonNotifier extends Notifier
{
    const SOUND_DEFAULT = 'data/houston.wav';

    private $soundFail;
    private $soundSuccess;
    private $volume;

    public function __construct($soundFail = self::SOUND_DEFAULT, $soundSuccess = null, $volume = 1)
    {
        $this->soundFail    = $this->normalizePath($soundFail);
        $this->soundSuccess = $this->normalizePath($soundSuccess);
        $this->volume       = (float) $volume;

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
        $p = new Process(sprintf('afplay "%s" --volume %s', $file, $this->volume));
        $p->setTimeout(30);

        $p->run();
    }
    protected function normalizePath($path)
    {
        if (!is_string($path)) {
            return null;
        }

        // if given path is relative, create path relative to package root dir
        if (0 !== strpos($path, '/')) {
            $path = __DIR__ . '/../../../' . $path;
        }

        if (!file_exists($path)) {
            throw new \InvalidArgumentException(sprintf('Soundfile %s could not be found.'), $path);
        }

        return $path;
    }
}
