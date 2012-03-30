<?php

/*
 * (c) Andreas Hucks <andreas.hucks@duochrome.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Duochrome\Sismo;

use Symfony\Component\Process\Process;
use Sismo\Notifier;
use Sismo\Commit;

/**
 * Calls mission control if there is a problem.
 * **Mac only** for now.
 */
class HoustonNotifier extends Notifier
{
    const SOUND_DEFAULT = 'houston.wav';

    private $soundFail;
    private $soundSuccess;
    private $volume;

    /**
     * Constructor.
     *
     * @param string|null $soundFail    Audio file played when a build fails, absolute path or relative to data dir
     * @param string|null $soundSuccess Audio file played when a build succeeds, absolute path or relative to data dir
     * @param int         $volume       Volume to play the sounds at, between 0 = mute and 1 = current system volume
     */
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

    /**
     * {@inheritdoc}
     */
    public function notify(Commit $commit)
    {
        if (!$commit->isSuccessful() && null !== $this->soundFail) {
            $this->play($this->soundFail);
        } elseif ($commit->isSuccessful() && null !== $this->soundSuccess) {
            $this->play($this->soundSuccess);
        }
    }

    /**
     * Play an audio file.
     *
     * @param string $file The filepath
     */
    protected function play($file)
    {
        $p = new Process(sprintf('afplay "%s" --volume %s', $file, $this->volume));
        $p->setTimeout(30);

        $p->run();
    }

    /**
     *
     *
     * @param string $path
     * @return null|string
     * @throws \InvalidArgumentException When the file does not exist
     */
    protected function normalizePath($path)
    {
        if (!is_string($path)) {
            return null;
        }

        // if given path is relative, it is meant relative to package root dir
        if (0 !== strpos($path, '/')) {
            $path = __DIR__ . '/../../../data/' . $path;
        }

        if (!file_exists($path)) {
            throw new \InvalidArgumentException(sprintf('Soundfile %s could not be found.'), $path);
        }

        return $path;
    }
}
