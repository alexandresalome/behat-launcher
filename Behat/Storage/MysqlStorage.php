<?php

namespace Alex\BehatLauncherBundle\Behat\Storage;

use Alex\BehatLauncherBundle\Behat\LazyRunUnitList;
use Alex\BehatLauncherBundle\Behat\Project;
use Alex\BehatLauncherBundle\Behat\Run;
use Alex\BehatLauncherBundle\Behat\RunUnit;
use Alex\BehatLauncherBundle\Behat\RunUnitList;
use Doctrine\DBAL\Connection;

class MysqlStorage implements RunStorageInterface
{
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function initDb()
    {
        $this->connection->exec('CREATE TABLE IF NOT EXISTS bl_run (
            id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(64),
            project_name VARCHAR(64) NOT NULL,
            properties TEXT NOT NULL
        )');
        $this->connection->exec('CREATE TABLE IF NOT EXISTS bl_run_unit (
            id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
            run_id INTEGER NOT NULL,
            feature TEXT NOT NULL,
            created_at DATETIME NOT NULL,
            started_at DATETIME,
            finished_at DATETIME,
            return_code INTEGER,
            output_files TEXT
        )');
    }

    /**
     * {@inheritdoc}
     */
    public function saveRun(Run $run)
    {
        $this->initDb();

        // Just UPDATE title
        if ($run->getId()) {
            $stmt = $this->connection
                ->prepare('UPDATE bl_run SET title = :title WHERE id = :id')
                ->setParameters(array(
                    'title' => $run->getTitle(),
                    'id' => $run->getId()
                ))
            ;

            $stmt->execute();

            return $this;
        }

        // Insert the run
        $stmt = $this->connection->prepare('INSERT INTO bl_run (title, project_name, properties) VALUES (:title, :project_name, :properties)');
        $stmt->bindValue('title', $run->getTitle());
        $stmt->bindValue('project_name', $run->getProjectName());
        $stmt->bindValue('properties', json_encode($run->getProperties()));
        $stmt->execute();

        $run->setId($this->connection->lastInsertId());

        // Insert units
        foreach ($run->getUnits() as $unit) {
            $stmt = $this->connection->prepare('
                INSERT INTO bl_run_unit
                    (run_id, feature, created_at, started_at, finished_at, return_code, output_files)
                VALUES
                    (:run_id, :feature, :created_at, :started_at, :finished_at, :return_code, :output_files)
            ');

            $stmt->bindValue('run_id', $run->getId());
            $stmt->bindValue('feature', $unit->getFeature());
            $stmt->bindValue('created_at', $unit->getCreatedAt(), "datetime");
            $stmt->bindValue('started_at', $unit->getStartedAt(), "datetime");
            $stmt->bindValue('finished_at', $unit->getFinishedAt(), "datetime");
            $stmt->bindValue('return_code', $unit->getReturnCode());
            $stmt->bindValue('output_files', $unit->getOutputFiles());

            $stmt->execute();
            $unit->setId($this->connection->lastInsertId());
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function saveRunUnit(RunUnit $unit)
    {
        $this->initDb();

        if (!$unit->getId()) {
            throw new \RuntimeException('Cannot save run unit: no ID set in instance.');
        }

        $stmt = $this->connection->prepare('UPDATE bl_run_unit SET started_at = :started_at, finished_at = :finished_at, return_code = :return_code, output_files = :output_files WHERE id = :id');
        $stmt->bindValue('started_at', $unit->getStartedAt(), "datetime");
        $stmt->bindValue('finished_at', $unit->getFinishedAt(), "datetime");
        $stmt->bindValue('output_files', json_encode($unit->getOutputFiles()));
        $stmt->bindValue('return_code', json_encode($unit->getReturnCode()));
        $stmt->bindValue('id', $unit->getId());

        $stmt->execute();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUnits(Run $run)
    {
        $this->initDb();

        $stmt = $this->connection->prepare('
            SELECT
                id, feature, created_at, started_at, finished_at, return_code, output_files
            FROM
                bl_run_unit
            WHERE
                run_id = :run_id
        ');

        $stmt->bindValue('run_id', $run->getId());

        $stmt->execute();
        $units = array();
        while ($row = $stmt->fetch()) {
            $unit = new RunUnit();
            $unit
                ->setRun($run)
                ->setId($row['id'])
                ->setFeature($row['feature'])
                ->setCreatedAt(new \DateTime($row['created_at']))
                ->setStartedAt($row['started_at'] !== null ? new \DateTime($row['started_at']) : null)
                ->setFinishedAt($row['finished_at'] !== null ? new \DateTime($row['finished_at']) : null)
                ->setReturnCode($row['return_code'])
                ->setOutputFiles(json_decode($row['output_files'], true))
            ;

            $units[] = $unit;
        }

        return new RunUnitList($units);
    }

    /**
     * {@inheritdoc}
     */
    public function getRunnableUnit(Project $project = null)
    {
        $this->initDb();

        $whereProject = '';
        if ($project) {
            $whereProject = 'AND R.project_name = :project_name';
        }

        $this->connection->beginTransaction();

        $stmt = $this->connection->prepare('
            SELECT
                U.id AS id,
                U.feature AS feature,
                U.created_at AS created_at,
                U.finished_at AS finished_at,
                U.return_code AS return_code,
                U.output_files AS output_files,
                R.id AS run_id,
                R.title AS run_title,
                R.project_name AS run_project_name,
                R.properties AS run_properties
            FROM
                bl_run R
                INNER JOIN bl_run_unit U ON R.id = U.run_id
            WHERE U.started_at IS NULL '.$whereProject.'
            ORDER BY U.created_at ASC
            LIMIT 1
        ');

        if ($project) {
            $stmt->bindValue('project_name', $project->getName());
        }

        $stmt->execute();

        if (!$row = $stmt->fetch()) {
            return; // nothing to process
        }

        $stmt = $this->connection->prepare('UPDATE bl_run_unit SET started_at = NOW() WHERE id = :id');
        $stmt->bindValue('id', $row['id']);
        $stmt->execute();

        $this->connection->commit();

        $run = new Run();
        $run
            ->setId($row['run_id'])
            ->setTitle($row['run_title'])
            ->setProjectName($row['run_project_name'])
            ->setProperties(json_decode($row['run_properties'], true))
        ;

        $unit = new RunUnit();
        $unit
            ->setRun($run)
            ->setId($row['id'])
            ->setFeature($row['feature'])
            ->setCreatedAt(new \DateTime($row['created_at']))
            ->setStartedAt(new \DateTime()) // UPDATEd above
            ->setFinishedAt($row['finished_at'] !== null ? new \DateTime($row['finished_at']) : null)
            ->setReturnCode($row['return_code'])
            ->setOutputFiles(json_decode($row['output_files'], true))
        ;

        return $unit;
    }

    /**
     * {@inheritdoc}
     */
    public function getRun($id)
    {
        $this->initDb();

        $runs = $this->getRunsByWhere('R.id = :id', array('id' => $id));

        if (count($runs) == 0) {
            throw new \InvalidArgumentException(sprintf('No run found with ID "%s".', $id));
        }

        return $runs[0];
    }

    /**
     * {@inheritdoc}
     */
    public function getRuns(Project $project = null, $offset = 0, $limit = 100)
    {
        $this->initDb();

        if (null === $project) {
            return $this->getRunsByWhere('1 = 1');
        }

        return $this->getRunsByWhere('R.project_name = :project_name', array('project_name' => $project->getName()));
    }

    private function getRunsByWhere($where, array $params = array())
    {
        $stmt = $this->connection->prepare('
            SELECT
                R.id AS id,
                R.title AS title,
                R.project_name AS project_name,
                R.properties AS properties,
                SUM(IF(U.id IS NOT NULL AND U.started_at IS NULL, 1, 0)) AS count_pending,
                SUM(IF(U.id IS NOT NULL AND U.started_at IS NOT NULL AND U.finished_at IS NULL, 1, 0)) AS count_running,
                SUM(IF(U.id IS NOT NULL AND U.finished_at IS NOT NULL AND U.return_code = 0, 1, 0)) AS count_succeeded,
                SUM(IF(U.id IS NOT NULL AND U.finished_at IS NOT NULL AND U.return_code != 0, 1, 0)) AS count_failed,
                MIN(U.started_at) AS started_at,
                MAX(U.finished_at) AS finished_at
            FROM
                bl_run R
                LEFT JOIN bl_run_unit U ON R.id = U.run_id
            WHERE
                '.$where.'
            GROUP BY R.id
            ORDER BY U.created_at DESC
        ');

        foreach ($params as $name => $value) {
            $stmt->bindValue($name, $value);
        }

        $stmt->execute();

        $runs = array();
        while ($row = $stmt->fetch()) {
            $run = new Run();
            $run
                ->setId($row['id'])
                ->setTitle($row['title'])
                ->setProjectName($row['project_name'])
                ->setProperties(json_decode($row['properties'], true))
                ->setStartedAt(null !== $row['started_at'] ? new \DateTime($row['started_at']) : null)
                ->setFinishedAt(null !== $row['finished_at'] ? new \DateTime($row['finished_at']) : null)
            ;

            $list = new LazyRunUnitList($this, $run, $row['count_pending'], $row['count_running'], $row['count_succeeded'], $row['count_failed']);
            $run->setUnits($list);

            $runs[] = $run;
        }

        return $runs;
    }
}
