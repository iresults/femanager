<?php

declare(strict_types=1);
namespace In2code\Femanager\Domain\Model;

use In2code\Femanager\Utility\UserUtility;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUser;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Class User
 */
class User extends FrontendUser
{
    const TABLE_NAME = 'fe_users';

    /**
     * @var string
     */
    protected $txFemanagerChangerequest;

    /**
     * @var \DateTime
     */
    protected $crdate;

    /**
     * @var \DateTime
     */
    protected $tstamp;

    /**
     * @var bool
     */
    protected $disable;

    /**
     * @var bool
     */
    protected $txFemanagerConfirmedbyuser;

    /**
     * @var bool
     */
    protected $txFemanagerConfirmedbyadmin;

    /**
     * @var bool
     */
    protected $isOnline = false;

    /**
     * @var bool
     */
    protected $ignoreDirty = false;

    /**
     * @var int
     */
    protected $gender;

    /**
     * @var \DateTime
     */
    protected $dateOfBirth;

    /**
     * termsAndConditions
     *
     * @var bool
     */
    protected $terms = false;

    /**
     * the datetime the user accepted the terms
     *
     * @var \DateTime
     */
    protected $termsDateOfAcceptance;

    /**
     * @var string
     */
    protected $txExtbaseType;

    /**
     * Created Password in Cleartext (if generated Password)
     * will of course not be persistent and lives until runtime end
     *
     * @var string
     */
    protected $passwordAutoGenerated;

    /**
     * @var string
     */
    protected $state = '';

    public function removeAllUsergroups()
    {
        $this->usergroup = new ObjectStorage();
    }

    /**
     * @param string $txFemanagerChangerequest
     * @return User
     */
    public function setTxFemanagerChangerequest($txFemanagerChangerequest)
    {
        $this->txFemanagerChangerequest = $txFemanagerChangerequest;
        return $this;
    }

    /**
     * @return string
     */
    public function getTxFemanagerChangerequest()
    {
        return $this->txFemanagerChangerequest;
    }

    /**
     * @param \DateTime $crdate
     * @return User
     */
    public function setCrdate($crdate)
    {
        $this->crdate = $crdate;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCrdate()
    {
        if ($this->crdate === null) {
            // timestamp is zero
            $this->crdate = new \DateTime('01.01.1970');
        }
        return $this->crdate;
    }

    /**
     * @param \DateTime $tstamp
     * @return User
     */
    public function setTstamp($tstamp)
    {
        $this->tstamp = $tstamp;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getTstamp()
    {
        return $this->tstamp;
    }

    /**
     * @param bool $disable
     * @return User
     */
    public function setDisable($disable)
    {
        $this->disable = $disable;
        return $this;
    }

    /**
     * @return bool
     */
    public function getDisable()
    {
        return $this->disable;
    }

    /**
     * @param \bool $txFemanagerConfirmedbyadmin
     * @return User
     */
    public function setTxFemanagerConfirmedbyadmin($txFemanagerConfirmedbyadmin)
    {
        $this->txFemanagerConfirmedbyadmin = $txFemanagerConfirmedbyadmin;
        return $this;
    }

    /**
     * @return \bool
     */
    public function getTxFemanagerConfirmedbyadmin()
    {
        return $this->txFemanagerConfirmedbyadmin;
    }

    /**
     * @param \bool $txFemanagerConfirmedbyuser
     * @return User
     */
    public function setTxFemanagerConfirmedbyuser($txFemanagerConfirmedbyuser)
    {
        $this->txFemanagerConfirmedbyuser = $txFemanagerConfirmedbyuser;
        return $this;
    }

    /**
     * @return \bool
     */
    public function getTxFemanagerConfirmedbyuser()
    {
        return $this->txFemanagerConfirmedbyuser;
    }

    /**
     * @param bool $ignoreDirty
     * @return User
     */
    public function setIgnoreDirty($ignoreDirty)
    {
        $this->ignoreDirty = $ignoreDirty;
        return $this;
    }

    /**
     * @return bool
     */
    public function getIgnoreDirty()
    {
        return $this->ignoreDirty;
    }

    /**
     * Returns the gender
     *
     * @return int $gender
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Sets the gender
     *
     * @param int $gender
     * @return User
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
        return $this;
    }

    /**
     * Returns the dateOfBirth
     *
     * @return \DateTime $dateOfBirth
     */
    public function getDateOfBirth()
    {
        return $this->dateOfBirth;
    }

    /**
     * Sets the dateOfBirth
     *
     * @param \DateTime $dateOfBirth
     * @return User
     */
    public function setDateOfBirth($dateOfBirth)
    {
        if ($dateOfBirth instanceof \DateTime) {
            $dateOfBirth->setTime(0, 0, 0);
        }
        $this->dateOfBirth = $dateOfBirth;
        return $this;
    }

    /**
     * Returns, whether the user has accepted terms and conditions
     *
     * @return bool
     */
    public function isTerms(): bool
    {
        return $this->terms;
    }

    /**
     * Set whether the user has accepted terms and conditions
     *
     * @param bool $terms
     * @return User
     */
    public function setTerms(bool $terms)
    {
        $this->terms = $terms;
        $this->setTermsDateOfAcceptance();
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getTermsDateOfAcceptance()
    {
        return $this->termsDateOfAcceptance;
    }

    /**
     * set terms date to now if it's not set yet
     *
     * @return User
     */
    protected function setTermsDateOfAcceptance()
    {
        if ($this->termsDateOfAcceptance === null) {
            $now = new \DateTime();
            $this->termsDateOfAcceptance = $now;
        }
        return $this;
    }

    /**
     * @return bool
     */
    public function getIsOnline(): bool
    {
        return $this->isOnline();
    }

    /**
     * Check if last FE login was within the last 2h
     *
     * @return bool
     */
    public function isOnline(): bool
    {
        if ($this->getLastlogin() && method_exists($this->getLastlogin(), 'getTimestamp')
            && $this->getLastlogin()->getTimestamp() > (time() - 2 * 60 * 60)
            && UserUtility::checkFrontendSessionToUser($this)
        ) {
            return true;
        }
        return $this->isOnline;
    }

    /**
     * @param string $txExtbaseType
     * @return User
     */
    public function setTxExtbaseType($txExtbaseType)
    {
        $this->txExtbaseType = $txExtbaseType;
        return $this;
    }

    /**
     * @return string
     */
    public function getTxExtbaseType()
    {
        return $this->txExtbaseType;
    }

    public function getFirstImage()
    {
        $images = $this->getImage();
        foreach ($images as $image) {
            return $image;
        }
        return null;
    }

    /**
     * @param string $passwordAutoGenerated
     * @return User
     */
    public function setPasswordAutoGenerated($passwordAutoGenerated)
    {
        $this->passwordAutoGenerated = $passwordAutoGenerated;
        return $this;
    }

    /**
     * @return string
     */
    public function getPasswordAutoGenerated()
    {
        return $this->passwordAutoGenerated;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function setState(string $state): void
    {
        $this->state = $state;
    }

    /**
     * Workaround to disable persistence in updateAction
     *
     * @param null $propertyName
     * @return bool
     */
    public function _isDirty($propertyName = null)
    {
        return $this->getIgnoreDirty() ? false : parent::_isDirty($propertyName);
    }
}
