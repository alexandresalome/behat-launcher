<?php

namespace BehatLauncher\Extensions\Core\Twig;

use Symfony\Component\Translation\TranslatorInterface;

class DateExtension extends \Twig_Extension
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('dateinterval', array($this, 'getDateInterval')),
        );
    }

    public function getDateInterval($diff)
    {
        if ($diff instanceof \DateTime) {
            $diff = $diff->diff(new \DateTime());
        }

        if ($diff->y > 0) {
            return $this->translator->transChoice('one:one year|many:%year% years', $diff->y, array('%year%' => $diff->y));
        }

        if ($diff->m > 0) {
            return $this->translator->transChoice('one:one month|many:%month% months', $diff->m, array('%month%' => $diff->m));
        }

        if ($diff->d > 0) {
            return $this->translator->transChoice('one:one day|many:%day% days', $diff->d, array('%day%' => $diff->d));
        }

        if ($diff->h > 0) {
            return $this->translator->transChoice('one:one hour|many:%hour% hours', $diff->h, array('%hour%' => $diff->h));
        }

        if ($diff->i > 0) {
            return $this->translator->transChoice('one:one minute|many:%minute% minutes', $diff->i, array('%minute%' => $diff->i));
        }

        if ($diff->s > 0) {
            return $this->translator->transChoice('one:one second|many:%second% seconds', $diff->s, array('%second%' => $diff->s));
        }

        return $this->translator->trans('no time', array());
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'date';
    }
}
