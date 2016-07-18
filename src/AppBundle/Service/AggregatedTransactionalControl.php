<?php
namespace AppBundle\Service;

/**
 * Manages multiple transaction controls.
 */
class AggregatedTransactionControl extends AbstractTransactionControl
{
    /**
     * @var TransactionControlInterface[]
     */
    private $controls;

    /**
     * @param TransactionControlInterface[] $controls
     */
    public function __construct(array $controls)
    {
        $this->controls = $controls;
    }

    /**
     * @inheritDoc
     */
    protected function doBegin()
    {
        /** @var TransactionControlInterface[] $began */
        $began = [];
        foreach ($this->controls as $control) {
            try {
                $control->begin();
                $began[] = $control;
            } catch (\Exception $ex) {
                foreach ($began as $startedControl) {
                    try {
                        $startedControl->rollback();
                    } catch (\Exception $subEx) {
                        //Whatever
                    }
                }
                throw $ex;
            }
        }
    }

    /**
     * @inheritDoc
     */
    protected function doRollback()
    {
        //Will throw only 1 exception if any occur.
        $exCaught = null;
        foreach ($this->controls as $control) {
            try {
                $control->rollback();
            } catch (\Exception $ex) {
                $exCaught = $ex;
            }
        }

        if ($exCaught) {
            throw $exCaught;
        }
    }

    /**
     * @inheritDoc
     */
    protected function doCommit()
    {
        /** @var TransactionControlInterface[] $committed */
        $committed = [];
        foreach ($this->controls as $control) {
            try {
                $control->commit();
                $committed[] = $control;
            } catch (\Throwable $ex) {
                //Rolling back uncommitted controls, nothing we can do with already committed.
                foreach ($this->controls as $someControl) {
                    if (!in_array($someControl, $committed, true)) {
                        try {
                            $someControl->rollback();
                        } catch (\Throwable $subEx) {
                            //Whatever
                        }
                    }
                }
                throw $ex;
            }
        }
    }

}