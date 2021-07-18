<?php

namespace AmeliaBooking\Infrastructure\Repository\User;

use AmeliaBooking\Domain\Collection\Collection;
use AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException;
use AmeliaBooking\Domain\Entity\Schedule\Period;
use AmeliaBooking\Domain\Entity\Schedule\SpecialDay;
use AmeliaBooking\Domain\Entity\Schedule\WeekDay;
use AmeliaBooking\Domain\Entity\User\AbstractUser;
use AmeliaBooking\Domain\Entity\User\Provider;
use AmeliaBooking\Domain\Factory\User\ProviderFactory;
use AmeliaBooking\Domain\Repository\User\ProviderRepositoryInterface;
use AmeliaBooking\Domain\Services\DateTime\DateTimeService;
use AmeliaBooking\Domain\ValueObjects\String\Status;
use AmeliaBooking\Infrastructure\Common\Exceptions\QueryExecutionException;
use AmeliaBooking\Infrastructure\Connection;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\Bookable\ExtrasTable;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\Booking\AppointmentsTable;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\Coupon\CouponsTable;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\Coupon\CouponsToServicesTable;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\Location\LocationsTable;
use AmeliaBooking\Infrastructure\WP\InstallActions\DB\User\WPUsersTable;

/**
 * Class ProviderRepository
 *
 * @package AmeliaBooking\Infrastructure\Repository
 */
class ProviderRepository extends UserRepository implements ProviderRepositoryInterface
{
    const FACTORY = ProviderFactory::class;

    /** @var string */
    protected $providerWeekDayTable;

    /** @var string */
    protected $providerPeriodTable;

    /** @var string */
    protected $providerPeriodServiceTable;

    /** @var string */
    protected $providerTimeOutTable;

    /** @var string */
    protected $providerSpecialDayTable;

    /** @var string */
    protected $providerSpecialDayPeriodTable;

    /** @var string */
    protected $providerSpecialDayPeriodServiceTable;

    /** @var string */
    protected $providerDayOffTable;

    /** @var string */
    protected $providerServicesTable;

    /** @var string */
    protected $providerLocationTable;

    /** @var string */
    protected $serviceTable;

    /** @var string */
    protected $providerViewsTable;

    /** @var string */
    protected $providersGoogleCalendarTable;

    /** @var string */
    protected $providersOutlookCalendarTable;

    /**
     * @param Connection $connection
     * @param string     $table
     * @param string     $providerWeekDayTable
     * @param string     $providerPeriodTable
     * @param string     $providerPeriodServiceTable
     * @param string     $providerTimeOutTable
     * @param string     $providerSpecialDayTable
     * @param string     $providerSpecialDayPeriodTable
     * @param string     $providerSpecialDayPeriodServiceTable
     * @param string     $providerDayOffTable
     * @param string     $providerServicesTable
     * @param string     $providerLocationTable
     * @param string     $serviceTable
     * @param string     $providerViewsTable
     * @param string     $providersGoogleCalendarTable
     * @param string     $providersOutlookCalendarTable
     */
    public function __construct(
        Connection $connection,
        $table,
        $providerWeekDayTable,
        $providerPeriodTable,
        $providerPeriodServiceTable,
        $providerTimeOutTable,
        $providerSpecialDayTable,
        $providerSpecialDayPeriodTable,
        $providerSpecialDayPeriodServiceTable,
        $providerDayOffTable,
        $providerServicesTable,
        $providerLocationTable,
        $serviceTable,
        $providerViewsTable,
        $providersGoogleCalendarTable,
        $providersOutlookCalendarTable
    ) {
        parent::__construct($connection, $table);

        $this->providerWeekDayTable = $providerWeekDayTable;
        $this->providerPeriodTable = $providerPeriodTable;
        $this->providerPeriodServiceTable = $providerPeriodServiceTable;
        $this->providerTimeOutTable = $providerTimeOutTable;
        $this->providerSpecialDayTable = $providerSpecialDayTable;
        $this->providerSpecialDayPeriodTable = $providerSpecialDayPeriodTable;
        $this->providerSpecialDayPeriodServiceTable = $providerSpecialDayPeriodServiceTable;
        $this->providerDayOffTable = $providerDayOffTable;
        $this->providerServicesTable = $providerServicesTable;
        $this->providerLocationTable = $providerLocationTable;
        $this->serviceTable = $serviceTable;
        $this->providerViewsTable = $providerViewsTable;
        $this->providersGoogleCalendarTable = $providersGoogleCalendarTable;
        $this->providersOutlookCalendarTable = $providersOutlookCalendarTable;
    }

    /**
     * @param int $id
     *
     * @return Provider
     * @throws QueryExecutionException
     */
    public function getById($id)
    {
        try {
            $statement = $this->connection->prepare(
                "SELECT
                    u.id AS user_id,
                    u.status AS user_status,
                    u.externalId AS external_id,
                    u.firstName AS user_firstName,
                    u.lastName AS user_lastName,
                    u.email AS user_email,
                    u.note AS note,
                    u.phone AS phone,
                    u.pictureFullPath AS picture_full_path,
                    u.pictureThumbPath AS picture_thumb_path,
                    u.zoomUserId AS user_zoom_user_id,
                    gd.id AS google_calendar_id,
                    gd.token AS google_calendar_token,
                    gd.calendarId AS google_calendar_calendar_id,
                    od.id AS outlook_calendar_id,
                    od.token AS outlook_calendar_token,
                    od.calendarId AS outlook_calendar_calendar_id
                FROM {$this->table} u
                LEFT JOIN {$this->providersGoogleCalendarTable} gd ON gd.userId = u.id
                LEFT JOIN {$this->providersOutlookCalendarTable} od ON od.userId = u.id
                WHERE u.type = :type AND u.id = :userId
                ORDER BY u.id"
            );

            $type = AbstractUser::USER_ROLE_PROVIDER;

            $statement->bindParam(':type', $type);
            $statement->bindParam(':userId', $id);

            $statement->execute();

            $providerRows = [];
            $serviceRows = [];
            $providerServiceRows = [];

            while ($row = $statement->fetch()) {
                $this->parseUserRow($row, $providerRows, $serviceRows, $providerServiceRows);
            }
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to find by id in ' . __CLASS__, $e->getCode(), $e);
        }

        return call_user_func([static::FACTORY, 'createCollection'], $providerRows, $serviceRows, $providerServiceRows)->getItem($id);
    }

    /**
     *
     * @return Collection
     * @throws QueryExecutionException
     */
    public function getAll()
    {
        try {
            $statement = $this->connection->prepare(
                "SELECT
                    u.id AS user_id,
                    u.status AS user_status,
                    u.externalId AS external_id,
                    u.firstName AS user_firstName,
                    u.lastName AS user_lastName,
                    u.email AS user_email,
                    u.note AS note,
                    u.phone AS phone,
                    u.pictureFullPath AS picture_full_path,
                    u.pictureThumbPath AS picture_thumb_path,
                    lt.locationId AS user_locationId
                FROM {$this->table} u
                LEFT JOIN {$this->providerLocationTable} lt ON lt.userId = u.id
                WHERE u.type = :type
                ORDER BY CONCAT(u.firstName, ' ', u.lastName)"
            );

            $type = AbstractUser::USER_ROLE_PROVIDER;

            $statement->bindParam(':type', $type);

            $statement->execute();

            $providerRows = [];
            $serviceRows = [];
            $providerServiceRows = [];

            while ($row = $statement->fetch()) {
                $this->parseUserRow($row, $providerRows, $serviceRows, $providerServiceRows);
            }
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to find by id in ' . __CLASS__, $e->getCode(), $e);
        }

        return call_user_func([static::FACTORY, 'createCollection'], $providerRows, $serviceRows, $providerServiceRows);
    }

    /**
     * @param array $criteria
     * @param int   $itemsPerPage
     *
     * @return Collection
     * @throws QueryExecutionException
     * @throws InvalidArgumentException
     */
    public function getFiltered($criteria, $itemsPerPage)
    {
        try {
            $wpUserTable = WPUsersTable::getTableName();

            $params[':type'] = AbstractUser::USER_ROLE_PROVIDER;

            $order = '';
            if (!empty($criteria['sort'])) {
                $orderColumn = 'CONCAT(u.firstName, " ", u.lastName)';
                $orderDirection = $criteria['sort'][0] === '-' ? 'DESC' : 'ASC';
                $order = "ORDER BY {$orderColumn} {$orderDirection}";
            }

            $where = [];

            if (!empty($criteria['search'])) {
                $params[':search1'] = $params[':search2'] = $params[':search3'] = $params[':search4'] =
                    "%{$criteria['search']}%";

                $where[] = "u.id IN(
                    SELECT DISTINCT(user.id)
                        FROM {$this->table} user
                        LEFT JOIN {$wpUserTable} wpUser ON user.externalId = wpUser.ID
                        WHERE (CONCAT(user.firstName, ' ', user.lastName) LIKE :search1
                            OR wpUser.display_name LIKE :search2
                            OR user.email LIKE :search3
                            OR user.note LIKE :search4)
                    )";
            }

            if (!empty($criteria['services'])) {
                $queryServices = [];

                foreach ((array)$criteria['services'] as $index => $value) {
                    $param = ':service' . $index;
                    $queryServices[] = $param;
                    $params[$param] = $value;
                }

                $where[] = "u.id IN (
                    SELECT pst.userId FROM {$this->providerServicesTable} pst
                    WHERE pst.userId = u.id AND pst.serviceId IN (" . implode(', ', $queryServices) . ')
                )';
            }

            if (!empty($criteria['providers'])) {
                $queryProviders = [];

                foreach ((array)$criteria['providers'] as $index => $value) {
                    $param = ':provider' . $index;
                    $queryProviders[] = $param;
                    $params[$param] = $value;
                }

                $where[] = 'u.id IN (' . implode(', ', $queryProviders) . ')';
            }

            if (!empty($criteria['location'])) {
                $params[':location'] = $criteria['location'];

                $where[] = "u.id IN (
                    SELECT plt.userId FROM {$this->providerLocationTable} plt
                    WHERE plt.userId = u.id AND plt.locationId = :location)";
            }

            $where[] = "u.status NOT LIKE 'disabled'";

            $where = $where ? ' AND ' . implode(' AND ', $where) : '';

            $limit = $this->getLimit(
                !empty($criteria['page']) ? (int)$criteria['page'] : 0,
                (int)$itemsPerPage
            );

            $statement = $this->connection->prepare(
                "SELECT u.*
                    FROM {$this->table} u
                    WHERE u.type = :type $where
                {$order}
                {$limit}"
            );

            $statement->execute($params);

            $rows = $statement->fetchAll();
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to get data from ' . __CLASS__, $e->getCode(), $e);
        }

        $items = new Collection();

        foreach ($rows as $row) {
            $items->addItem(call_user_func([static::FACTORY, 'create'], $row), $row['id']);
        }

        return $items;
    }

    /**
     *
     * @return Collection
     * @throws QueryExecutionException
     */
    public function getAllWithServices()
    {
        try {
            $statement = $this->connection->prepare(
                "SELECT
                    u.id AS user_id,
                    u.status AS user_status,
                    u.externalId AS external_id,
                    u.firstName AS user_firstName,
                    u.lastName AS user_lastName,
                    u.email AS user_email,
                    u.note AS note,
                    u.phone AS phone,
                    u.pictureFullPath AS picture_full_path,
                    u.pictureThumbPath AS picture_thumb_path,
                    u.zoomUserId AS user_zoom_user_id,
                    lt.locationId AS user_locationId,
                    st.serviceId AS service_id,
                    st.price AS service_price,
                    st.minCapacity AS service_minCapacity,
                    st.maxCapacity AS service_maxCapacity,
                    s.name AS service_name,
                    s.description AS service_description,
                    s.color AS service_color,
                    s.status AS service_status,
                    s.categoryId AS service_categoryId,
                    s.duration AS service_duration,
                    s.bringingAnyone AS service_bringingAnyone,
                    s.show AS service_show,
                    s.aggregatedPrice AS service_aggregatedPrice,
                    s.pictureFullPath AS service_picture_full,
                    s.pictureThumbPath AS service_picture_thumb,
                    s.recurringCycle AS service_recurringCycle,
                    s.recurringSub AS service_recurringSub,
                    s.recurringPayment AS service_recurringPayment,
                    s.settings AS service_settings,
                    s.translations AS service_translations,
                    s.deposit AS service_deposit,
                    s.depositPayment AS service_depositPayment,
                    s.depositPerPerson AS service_depositPerPerson
                FROM {$this->table} u
                LEFT JOIN {$this->providerLocationTable} lt ON lt.userId = u.id
                LEFT JOIN {$this->providerServicesTable} st ON st.userId = u.id
                LEFT JOIN {$this->serviceTable} s ON s.id = st.serviceId
                WHERE u.type = :type
                ORDER BY CONCAT(u.firstName, ' ', u.lastName)"
            );

            $type = AbstractUser::USER_ROLE_PROVIDER;

            $statement->bindParam(':type', $type);

            $statement->execute();

            $providerRows = [];
            $serviceRows = [];
            $providerServiceRows = [];

            while ($row = $statement->fetch()) {
                $this->parseUserRow($row, $providerRows, $serviceRows, $providerServiceRows);
            }

            $providers = ProviderFactory::createCollection($providerRows, $serviceRows, $providerServiceRows);

            if (!$providers->length()) {
                return new Collection();
            }

            $providerIds = implode(', ', $providers->keys());

            $statement = $this->connection->prepare(
                "SELECT
                    u.id AS user_id,
                    u.firstName AS user_firstName,
                    u.lastName AS user_lastName,
                    u.email AS user_email,
                    wdt.id AS weekDay_id,
                    wdt.dayIndex AS weekDay_dayIndex,
                    wdt.startTime AS weekDay_startTime,
                    wdt.endTime As weekDay_endTime,
                    pt.id AS period_id,
                    pt.startTime AS period_startTime,
                    pt.endTime AS period_endTime,
                    pt.locationId AS period_locationId,
                    pst.id AS periodService_id,
                    pst.serviceId AS periodService_serviceId,
                    sdt.id AS specialDay_id,
                    sdt.startDate AS specialDay_startDate,
                    sdt.endDate As specialDay_endDate,
                    sdpt.id AS specialDayPeriod_id,
                    sdpt.startTime AS specialDayPeriod_startTime,
                    sdpt.endTime AS specialDayPeriod_endTime,
                    sdpt.locationId AS specialDayPeriod_locationId,
                    sdpst.id AS specialDayPeriodService_id,
                    sdpst.serviceId AS specialDayPeriodService_serviceId
                FROM {$this->table} u
                LEFT JOIN {$this->providerWeekDayTable} wdt ON wdt.userId = u.id
                LEFT JOIN {$this->providerPeriodTable} pt ON pt.weekDayId = wdt.id
                LEFT JOIN {$this->providerPeriodServiceTable} pst ON pst.periodId = pt.id
                LEFT JOIN {$this->providerSpecialDayTable} sdt ON sdt.userId = u.id
                LEFT JOIN {$this->providerSpecialDayPeriodTable} sdpt ON sdpt.specialDayId = sdt.id
                LEFT JOIN {$this->providerSpecialDayPeriodServiceTable} sdpst ON sdpst.periodId = sdpt.id
                WHERE u.id IN ({$providerIds})
                ORDER BY CONCAT(u.firstName, ' ', u.lastName)"
            );

            $statement->execute();

            $providerRows = [];
            $serviceRows = [];
            $providerServiceRows = [];

            while ($row = $statement->fetch()) {
                $this->parseUserRow($row, $providerRows, $serviceRows, $providerServiceRows);
            }

            /** @var Provider $provider */
            foreach (ProviderFactory::createCollection($providerRows, [], [])->getItems() as $provider) {
                $providers->getItem(
                    $provider->getId()->getValue()
                )->setWeekDayList($provider->getWeekDayList());

                $providers->getItem(
                    $provider->getId()->getValue()
                )->setSpecialDayList($provider->getSpecialDayList());
            }
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to find by id in ' . __CLASS__, $e->getCode(), $e);
        }

        return $providers;
    }

    /**
     *
     * @param array $criteria
     *
     * @return Collection
     * @throws QueryExecutionException
     */
    public function getByCriteriaWithSchedule($criteria)
    {
        $where = ['u.type = :type'];

        $params[':type'] = AbstractUser::USER_ROLE_PROVIDER;

        if (!empty($criteria['providers'])) {
            $queryProviders = [];

            foreach ((array)$criteria['providers'] as $index => $value) {
                $param = ':provider' . $index;
                $queryProviders[] = $param;
                $params[$param] = $value;
            }

            $where[] = 'u.id IN (' . implode(', ', $queryProviders) . ')';
        }

        $where = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        try {
            $statement = $this->connection->prepare(
                "SELECT
                    u.id AS user_id,
                    u.status AS user_status,
                    u.externalId AS external_id,
                    u.firstName AS user_firstName,
                    u.lastName AS user_lastName,
                    u.email AS user_email,
                    u.zoomUserId AS user_zoom_user_id,
                    u.countryPhoneIso AS user_countryPhoneIso,
                    u.note AS note,
                    u.phone AS phone,
                    u.pictureFullPath AS picture_full_path,
                    u.pictureThumbPath AS picture_thumb_path,
                    lt.locationId AS user_locationId,
                    wdt.id AS weekDay_id,
                    wdt.dayIndex AS weekDay_dayIndex,
                    wdt.startTime AS weekDay_startTime,
                    wdt.endTime As weekDay_endTime,
                    pt.id AS period_id,
                    pt.startTime AS period_startTime,
                    pt.endTime AS period_endTime,
                    pt.locationId AS period_locationId,
                    pst.id AS periodService_id,
                    pst.serviceId AS periodService_serviceId,
                    tot.id AS timeOut_id,
                    tot.startTime AS timeOut_startTime,
                    tot.endTime AS timeOut_endTime,
                    gd.id AS google_calendar_id,
                    gd.token AS google_calendar_token,
                    gd.calendarId AS google_calendar_calendar_id,
                    od.id AS outlook_calendar_id,
                    od.token AS outlook_calendar_token,
                    od.calendarId AS outlook_calendar_calendar_id
                FROM {$this->table} u
                LEFT JOIN {$this->providerLocationTable} lt ON lt.userId = u.id
                LEFT JOIN {$this->providersGoogleCalendarTable} gd ON gd.userId = u.id
                LEFT JOIN {$this->providersOutlookCalendarTable} od ON od.userId = u.id
                LEFT JOIN {$this->providerWeekDayTable} wdt ON wdt.userId = u.id
                LEFT JOIN {$this->providerPeriodTable} pt ON pt.weekDayId = wdt.id
                LEFT JOIN {$this->providerPeriodServiceTable} pst ON pst.periodId = pt.id
                LEFT JOIN {$this->providerSpecialDayTable} sdt ON sdt.userId = u.id
                LEFT JOIN {$this->providerSpecialDayPeriodTable} sdpt ON sdpt.specialDayId = sdt.id
                LEFT JOIN {$this->providerTimeOutTable} tot ON tot.weekDayId = wdt.id
                $where
                ORDER BY CONCAT(u.firstName, ' ', u.lastName), u.id, tot.weekDayId, wdt.dayIndex, pt.id"
            );

            $statement->execute($params);

            $providerRows = [];
            $serviceRows = [];
            $providerServiceRows = [];

            while ($row = $statement->fetch()) {
                $this->parseUserRow($row, $providerRows, $serviceRows, $providerServiceRows);
            }

            /** @var Collection $providers */
            $providers = call_user_func([static::FACTORY, 'createCollection'], $providerRows, $serviceRows, $providerServiceRows);

            $statement = $this->connection->prepare(
                "SELECT
                    u.id AS user_id,
                    u.status AS user_status,
                    u.externalId AS external_id,
                    u.firstName AS user_firstName,
                    u.lastName AS user_lastName,
                    u.email AS user_email,
                    u.note AS note,
                    u.phone AS phone,
                    u.pictureFullPath AS picture_full_path,
                    u.pictureThumbPath AS picture_thumb_path,
                    sdt.id AS specialDay_id,
                    sdt.startDate AS specialDay_startDate,
                    sdt.endDate As specialDay_endDate,
                    sdpt.id AS specialDayPeriod_id,
                    sdpt.startTime AS specialDayPeriod_startTime,
                    sdpt.endTime AS specialDayPeriod_endTime,
                    sdpt.locationId AS specialDayPeriod_locationId,
                    sdpst.id AS specialDayPeriodService_id,
                    sdpst.serviceId AS specialDayPeriodService_serviceId,
                    dot.id AS dayOff_id,
                    dot.name AS dayOff_name,
                    dot.startDate AS dayOff_startDate,
                    dot.endDate AS dayOff_endDate,
                    dot.repeat AS dayOff_repeat
                FROM {$this->table} u
                LEFT JOIN {$this->providerSpecialDayTable} sdt ON sdt.userId = u.id
                LEFT JOIN {$this->providerSpecialDayPeriodTable} sdpt ON sdpt.specialDayId = sdt.id
                LEFT JOIN {$this->providerSpecialDayPeriodServiceTable} sdpst ON sdpst.periodId = sdpt.id
                LEFT JOIN {$this->providerDayOffTable} dot ON dot.userId = u.id
                $where"
            );

            $providerRows = [];
            $serviceRows = [];
            $providerServiceRows = [];

            $statement->execute($params);

            while ($row = $statement->fetch()) {
                $this->parseUserRow($row, $providerRows, $serviceRows, $providerServiceRows);
            }

            /** @var Collection $providersWithSpecialDays */
            $providersWithSpecialDays = call_user_func([static::FACTORY, 'createCollection'], $providerRows, $serviceRows, $providerServiceRows);

            /** @var Provider $provider */
            foreach ($providersWithSpecialDays->getItems() as $provider) {
                $providers->getItem(
                    $provider->getId()->getValue()
                )->setDayOffList($provider->getDayOffList());

                $providers->getItem(
                    $provider->getId()->getValue()
                )->setSpecialDayList($provider->getSpecialDayList());
            }
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to find by id in ' . __CLASS__, $e->getCode(), $e);
        }

        return $providers;
    }

    /**
     * @param array $criteria
     *
     * @return mixed
     * @throws QueryExecutionException
     */
    public function getCount($criteria)
    {
        $params = [
            ':type'          => AbstractUser::USER_ROLE_PROVIDER,
            ':visibleStatus' => Status::VISIBLE,
            ':hiddenStatus'  => Status::HIDDEN,
        ];

        try {
            $wpUserTable = WPUsersTable::getTableName();

            $where = [];

            if (!empty($criteria['search'])) {
                $params[':search1'] = $params[':search2'] = $params[':search3'] = $params[':search4'] =
                    "%{$criteria['search']}%";

                $where[] = "u.id IN(
                    SELECT DISTINCT(user.id)
                        FROM {$this->table} user
                        LEFT JOIN {$wpUserTable} wpUser ON user.externalId = wpUser.ID
                        WHERE (CONCAT(user.firstName, ' ', user.lastName) LIKE :search1
                            OR wpUser.display_name LIKE :search2
                            OR user.email LIKE :search3
                            OR user.note LIKE :search4)
                    )";
            }

            if (!empty($criteria['services'])) {
                $queryServices = [];

                foreach ((array)$criteria['services'] as $index => $value) {
                    $param = ':service' . $index;
                    $queryServices[] = $param;
                    $params[$param] = $value;
                }

                $where[] = "u.id IN (
                    SELECT pst.userId FROM {$this->providerServicesTable} pst
                    WHERE pst.userId = u.id AND pst.serviceId IN (" . implode(', ', $queryServices) . ')
                )';
            }

            if (!empty($criteria['location'])) {
                $params[':location'] = $criteria['location'];

                $where[] = "u.id IN (
                    SELECT plt.userId FROM {$this->providerLocationTable} plt
                    WHERE plt.userId = u.id AND plt.locationId = :location)";
            }

            $where = $where ? ' AND ' . implode(' AND ', $where) : '';

            $statement = $this->connection->prepare(
                "SELECT COUNT(*) AS count
                    FROM {$this->table} u
                    WHERE u.type = :type AND u.status IN (:visibleStatus, :hiddenStatus) $where"
            );

            $statement->execute($params);

            $row = $statement->fetch()['count'];
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to get data from ' . __CLASS__, $e->getCode(), $e);
        }

        return $row;
    }

    /**
     * @param      $criteria
     *
     * @return Collection
     *
     * @throws InvalidArgumentException
     * @throws QueryExecutionException
     */
    public function getByCriteria($criteria)
    {
        $locationsTable = LocationsTable::getTableName();

        $params = [
            ':type' => AbstractUser::USER_ROLE_PROVIDER,
        ];

        $where = [];

        if (!empty($criteria['providerStatus'])) {
            $params[':providerStatus'] = $criteria['providerStatus'];

            $where[] = 'u.status = :providerStatus';
        }

        if (!empty($criteria['serviceStatus'])) {
            $params[':serviceStatus'] = $criteria['serviceStatus'];

            $where[] = 's.status = :serviceStatus';
        }

        if (!empty($criteria['services'])) {
            $queryServices = [];

            foreach ((array)$criteria['services'] as $index => $value) {
                $param = ':service' . $index;
                $queryServices[] = $param;
                $params[$param] = $value;
            }

            $where[] = 'st.serviceId IN (' . implode(', ', $queryServices) . ')';
        }

        if (!empty($criteria['providers'])) {
            $queryProviders = [];

            foreach ((array)$criteria['providers'] as $index => $value) {
                $param = ':provider' . $index;
                $queryProviders[] = $param;
                $params[$param] = $value;
            }

            $where[] = 'u.id IN (' . implode(', ', $queryProviders) . ')';
        }

        $where = $where ? ' AND ' . implode(' AND ', $where) : '';

        try {
            $statement = $this->connection->prepare(
                "SELECT
                    u.id AS user_id,
                    u.firstName AS user_firstName,
                    u.lastName AS user_lastName,
                    u.email AS user_email,
                    lt.locationId AS user_locationId,
                    wdt.id AS weekDay_id,
                    wdt.dayIndex AS weekDay_dayIndex,
                    wdt.startTime AS weekDay_startTime,
                    wdt.endTime As weekDay_endTime,
                    pt.id AS period_id,
                    pt.startTime AS period_startTime,
                    pt.endTime AS period_endTime,
                    pt.locationId AS period_locationId,
                    pst.id AS periodService_id,
                    pst.serviceId AS periodService_serviceId,
                    tot.id AS timeOut_id,
                    tot.startTime AS timeOut_startTime,
                    tot.endTime AS timeOut_endTime,
                    st.serviceId AS service_id,
                    st.price AS service_price,
                    st.minCapacity AS service_minCapacity,
                    st.maxCapacity AS service_maxCapacity,
                    s.name AS service_name,
                    s.description AS service_description,
                    s.color AS service_color,
                    s.status AS service_status,
                    s.categoryId AS service_categoryId,
                    s.duration AS service_duration,
                    s.aggregatedPrice AS service_aggregatedPrice,
                    s.bringingAnyone AS service_bringingAnyone,
                    s.pictureFullPath AS service_picture_full,
                    s.pictureThumbPath AS service_picture_thumb,
                    s.settings AS service_settings,
                    s.timeBefore AS service_timeBefore,
                    s.timeAfter AS service_timeAfter,
                    s.deposit AS service_deposit,
                    s.depositPayment AS service_depositPayment,
                    s.depositPerPerson AS service_depositPerPerson,
                    gd.id AS google_calendar_id,
                    gd.token AS google_calendar_token,
                    gd.calendarId AS google_calendar_calendar_id,
                    od.id AS outlook_calendar_id,
                    od.token AS outlook_calendar_token,
                    od.calendarId AS outlook_calendar_calendar_id
                FROM {$this->table} u
                INNER JOIN {$this->providerServicesTable} st ON st.userId = u.id
                LEFT JOIN {$this->serviceTable} s ON s.id = st.serviceId
                LEFT JOIN {$this->providerLocationTable} lt ON lt.userId = u.id
                LEFT JOIN {$locationsTable} l ON (lt.locationId = l.id AND l.status = 'visible')
                LEFT JOIN {$this->providersGoogleCalendarTable} gd ON gd.userId = u.id
                LEFT JOIN {$this->providersOutlookCalendarTable} od ON od.userId = u.id
                LEFT JOIN {$this->providerWeekDayTable} wdt ON wdt.userId = u.id
                LEFT JOIN {$this->providerPeriodTable} pt ON pt.weekDayId = wdt.id
                LEFT JOIN {$this->providerPeriodServiceTable} pst ON pst.periodId = pt.id
                LEFT JOIN {$this->providerTimeOutTable} tot ON tot.weekDayId = wdt.id
                WHERE u.type = :type {$where}
                ORDER BY tot.weekDayId, wdt.dayIndex"
            );

            $statement->execute($params);

            $providerRows = [];
            $serviceRows = [];
            $providerServiceRows = [];

            while ($row = $statement->fetch()) {
                $this->parseUserRow($row, $providerRows, $serviceRows, $providerServiceRows);
            }

            /** @var Collection $providers */
            $providers = call_user_func([static::FACTORY, 'createCollection'], $providerRows, $serviceRows, $providerServiceRows);

            $params = [
                ':type' => AbstractUser::USER_ROLE_PROVIDER,
            ];

            $where = [];

            if (!empty($criteria['providerStatus'])) {
                $params[':providerStatus'] = $criteria['providerStatus'];

                $where[] = 'u.status = :providerStatus';
            }

            if (!empty($criteria['providers'])) {
                $queryProviders = [];

                foreach ((array)$criteria['providers'] as $index => $value) {
                    $param = ':provider' . $index;
                    $queryProviders[] = $param;
                    $params[$param] = $value;
                }

                $where[] = 'u.id IN (' . implode(', ', $queryProviders) . ')';
            }

            $where = $where ? ' AND ' . implode(' AND ', $where) : '';

            $statement = $this->connection->prepare(
                "SELECT
                    u.id AS user_id,
                    u.firstName AS user_firstName,
                    u.lastName AS user_lastName,
                    u.email AS user_email,
                    sdt.id AS specialDay_id,
                    sdt.startDate AS specialDay_startDate,
                    sdt.endDate As specialDay_endDate,
                    sdpt.id AS specialDayPeriod_id,
                    sdpt.startTime AS specialDayPeriod_startTime,
                    sdpt.endTime AS specialDayPeriod_endTime,
                    sdpt.locationId AS specialDayPeriod_locationId,
                    sdpst.id AS specialDayPeriodService_id,
                    sdpst.serviceId AS specialDayPeriodService_serviceId,
                    dot.id AS dayOff_id,
                    dot.name AS dayOff_name,
                    dot.startDate AS dayOff_startDate,
                    dot.endDate AS dayOff_endDate,
                    dot.repeat AS dayOff_repeat
                FROM {$this->table} u
                LEFT JOIN {$this->providerSpecialDayTable} sdt ON sdt.userId = u.id
                LEFT JOIN {$this->providerSpecialDayPeriodTable} sdpt ON sdpt.specialDayId = sdt.id
                LEFT JOIN {$this->providerSpecialDayPeriodServiceTable} sdpst ON sdpst.periodId = sdpt.id
                LEFT JOIN {$this->providerDayOffTable} dot ON dot.userId = u.id
                WHERE u.type = :type {$where}"
            );

            $statement->execute($params);

            $providerRows = [];
            $serviceRows = [];
            $providerServiceRows = [];

            while ($row = $statement->fetch()) {
                $this->parseUserRow($row, $providerRows, $serviceRows, $providerServiceRows);
            }

            /** @var Collection $providers2 */
            $providersWithChanges = call_user_func(
                [static::FACTORY, 'createCollection'],
                $providerRows,
                $serviceRows,
                $providerServiceRows
            );

            /** @var Provider $provider */
            foreach ($providersWithChanges->getItems() as $provider) {
                if ($providers->keyExists($provider->getId()->getValue())) {
                    $providers->getItem(
                        $provider->getId()->getValue()
                    )->setDayOffList($provider->getDayOffList());

                    $providers->getItem(
                        $provider->getId()->getValue()
                    )->setSpecialDayList($provider->getSpecialDayList());
                }
            }

            /** @var Provider $provider */
            foreach ($providers->getItems() as $provider) {
                if (!empty($criteria['location']) &&
                    $provider->getLocationId() &&
                    $provider->getLocationId()->getValue() !== (int)$criteria['location']
                ) {
                    $hasLocation = false;

                    /** @var WeekDay $weekDay */
                    foreach ($provider->getWeekDayList()->getItems() as $weekDay) {
                        /** @var Period $period */
                        foreach ($weekDay->getPeriodList()->getItems() as $period) {
                            if ($period->getLocationId() &&
                                $period->getLocationId()->getValue() === (int)$criteria['location']
                            ) {
                                $hasLocation = true;
                                break;
                            }
                        }
                    }

                    /** @var SpecialDay $specialDay */
                    foreach ($provider->getSpecialDayList()->getItems() as $specialDay) {
                        /** @var Period $period */
                        foreach ($specialDay->getPeriodList()->getItems() as $period) {
                            if ($period->getLocationId() &&
                                $period->getLocationId()->getValue() === (int)$criteria['location']
                            ) {
                                $hasLocation = true;
                                break;
                            }
                        }
                    }

                    if (!$hasLocation) {
                        $providers->deleteItem($provider->getId()->getValue());
                    }
                }
            }
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to find by id in ' . __CLASS__, $e->getCode(), $e);
        }

        return $providers;
    }

    /**
     * @param      $criteria
     *
     * @return Collection
     * @throws InvalidArgumentException
     * @throws QueryExecutionException
     */
    public function getWithServicesAndExtrasAndCoupons($criteria)
    {
        $extrasTable = ExtrasTable::getTableName();
        $couponToServicesTable = CouponsToServicesTable::getTableName();
        $couponsTable = CouponsTable::getTableName();

        $params = [
            ':type'          => AbstractUser::USER_ROLE_PROVIDER,
            ':userStatus'    => Status::VISIBLE,
            ':serviceStatus' => Status::VISIBLE
        ];

        $where = [];

        foreach ((array)$criteria as $index => $value) {
            $params[':service' . $index] = $value['serviceId'];
            $params[':provider' . $index] = $value['providerId'];

            if ($value['couponId']) {
                $params[':coupon' . $index] = $value['couponId'];
                $params[':couponStatus' . $index] = Status::VISIBLE;
            }

            $where[] = "(s.id = :service$index AND u.id = :provider$index"
                . ($value['couponId'] ? " AND c.id = :coupon$index AND c.status = :couponStatus$index" : '') . ')';
        }

        $where = $where ? ' AND ' . implode(' OR ', $where) : '';

        try {
            $statement = $this->connection->prepare(
                "SELECT
                    u.id AS user_id,
                    u.firstName AS user_firstName,
                    u.lastName AS user_lastName,
                    u.email AS user_email,
                    st.serviceId AS service_id,
                    st.price AS service_price,
                    st.minCapacity AS service_minCapacity,
                    st.maxCapacity AS service_maxCapacity,
                    s.name AS service_name,
                    s.description AS service_description,
                    s.color AS service_color,
                    s.status AS service_status,
                    s.categoryId AS service_categoryId,
                    s.duration AS service_duration,
                    s.bringingAnyone AS service_bringingAnyone,
                    s.pictureFullPath AS service_picture_full,
                    s.pictureThumbPath AS service_picture_thumb,
                    s.aggregatedPrice AS service_aggregatedPrice,
                    s.recurringPayment AS service_recurringPayment,
                    s.translations AS service_translations,
                    s.timeBefore AS service_timeBefore,
                    s.timeAfter AS service_timeAfter,
                    s.deposit AS service_deposit,
                    s.depositPayment AS service_depositPayment,
                    s.depositPerPerson AS service_depositPerPerson,
                    e.id AS extra_id,
                    e.name AS extra_name,
                    e.price AS extra_price,
                    e.maxQuantity AS extra_maxQuantity,
                    e.duration AS extra_duration,
                    e.description AS extra_description,
                    e.position AS extra_position,
                    e.aggregatedPrice AS extra_aggregatedPrice,
                    c.id AS coupon_id,
                    c.code AS coupon_code,
                    c.discount AS coupon_discount,
                    c.deduction AS coupon_deduction,
                    c.limit AS coupon_limit,
                    c.customerLimit AS coupon_customerLimit,
                    c.status AS coupon_status
                FROM {$this->table} u
                INNER JOIN {$this->providerServicesTable} st ON st.userId = u.id
                INNER JOIN {$this->serviceTable} s ON s.id = st.serviceId
                LEFT JOIN {$extrasTable} e ON e.serviceId = s.id
                LEFT JOIN {$couponToServicesTable} cs ON cs.serviceId = s.id
                LEFT JOIN {$couponsTable} c ON c.id = cs.couponId
                WHERE u.status = :userStatus AND s.status = :serviceStatus AND u.type = :type $where"
            );

            $statement->execute($params);

            $providerRows = [];
            $serviceRows = [];
            $providerServiceRows = [];

            while ($row = $statement->fetch()) {
                $this->parseUserRow($row, $providerRows, $serviceRows, $providerServiceRows);
            }
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to find by id in ' . __CLASS__, $e->getCode(), $e);
        }

        return call_user_func([static::FACTORY, 'createCollection'], $providerRows, $serviceRows, $providerServiceRows);
    }

    /**
     * Returns array of available (currently working) Providers where keys are Provider ID's and array values are
     * Working Hours Data
     *
     * @param $dayIndex
     *
     * @return array
     * @throws QueryExecutionException
     */
    public function getAvailable($dayIndex)
    {
        $currentDateTime = "STR_TO_DATE('" . DateTimeService::getNowDateTime() . "', '%Y-%m-%d %H:%i:%s')";

        $params = [
            ':dayIndex' => $dayIndex === 0 ? 7 : $dayIndex,
            ':type'     => AbstractUser::USER_ROLE_PROVIDER
        ];

        try {
            $statement = $this->connection->prepare("SELECT
                u.id AS user_id,
                u.firstName AS user_firstName,
                u.lastName AS user_lastName,
                wdt.id AS weekDay_id,
                wdt.dayIndex AS weekDay_dayIndex,
                wdt.startTime AS weekDay_startTime,
                wdt.endTime AS weekDay_endTime,
                pt.id AS period_id,
                pt.startTime AS period_startTime,
                pt.endTime AS period_endTime
              FROM {$this->table} u
              LEFT JOIN {$this->providerWeekDayTable} wdt ON wdt.userId = u.id
              LEFT JOIN {$this->providerPeriodTable} pt ON pt.weekDayId = wdt.id
              WHERE u.type = :type AND
              wdt.dayIndex = :dayIndex AND
              ((
              {$currentDateTime} >= wdt.startTime AND
              {$currentDateTime} <= wdt.endTime AND
              pt.startTime IS NULL AND
              pt.endTime IS NULL
              ) OR (
              {$currentDateTime} >= pt.startTime AND
              {$currentDateTime} <= pt.endTime AND
              pt.startTime IS NOT NULL AND
              pt.endTime IS NOT NULL
              ))");

            $statement->execute($params);

            $rows = $statement->fetchAll();
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to find by id in ' . __CLASS__, $e->getCode(), $e);
        }

        $result = [];

        foreach ($rows as $row) {
            if (!array_key_exists($row['user_id'], $result)) {
                $result[$row['user_id']] = $row;
            }

            $result[$row['user_id']]['periods'][$row['period_id']] = [
                'startTime' => $row['period_startTime'],
                'endTime'   => $row['period_endTime']
            ];
        }

        return $result;
    }

    /**
     * Returns array of available (currently working) Providers where keys are Provider ID's and array values are
     * Working Hours Data on special day
     *
     * @return array
     * @throws QueryExecutionException
     */
    public function getOnSpecialDay()
    {
        $currentDateTime = "STR_TO_DATE('" . DateTimeService::getNowDateTime() . "', '%Y-%m-%d %H:%i:%s')";
        $currentDateString = DateTimeService::getNowDate();

        $params = [
            ':type' => AbstractUser::USER_ROLE_PROVIDER
        ];

        try {
            $statement = $this->connection->prepare("SELECT
                u.id AS user_id,
                u.firstName AS user_firstName,
                u.lastName AS user_lastName,
                IF (
                    {$currentDateTime} >= STR_TO_DATE(CONCAT(DATE_FORMAT(sdt.startDate, '%Y-%m-%d'), ' 00:00:00'), '%Y-%m-%d %H:%i:%s') AND
                    {$currentDateTime} <= DATE_ADD(STR_TO_DATE(CONCAT(DATE_FORMAT(sdt.endDate, '%Y-%m-%d'), ' 00:00:00'), '%Y-%m-%d %H:%i:%s'), INTERVAL 1 DAY) AND
                    {$currentDateTime} >= STR_TO_DATE(CONCAT('{$currentDateString}', ' ', DATE_FORMAT(sdpt.startTime, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') AND
                    {$currentDateTime} <= STR_TO_DATE(CONCAT('{$currentDateString}', ' ', DATE_FORMAT(sdpt.endTime, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s'),
                    1,
                    0
                ) AS available
              FROM {$this->table} u
              INNER JOIN {$this->providerSpecialDayTable} sdt ON sdt.userId = u.id
              INNER JOIN {$this->providerSpecialDayPeriodTable} sdpt ON sdpt.specialDayId = sdt.id
              WHERE u.type = :type AND
              STR_TO_DATE('{$currentDateString}', '%Y-%m-%d') BETWEEN sdt.startDate AND sdt.endDate
              ");

            $statement->execute($params);

            $rows = $statement->fetchAll();
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to find by id in ' . __CLASS__, $e->getCode(), $e);
        }

        $result = [];

        foreach ($rows as $row) {
            if (!array_key_exists($row['user_id'], $result)) {
                $result[$row['user_id']] = $row;
            }
        }

        return $result;
    }

    /**
     * @param $dayIndex
     *
     * @return array
     * @throws QueryExecutionException
     */
    public function getOnBreak($dayIndex)
    {
        $currentDateTime = "STR_TO_DATE('" . DateTimeService::getNowDateTime() . "', '%Y-%m-%d %H:%i:%s')";

        $params = [
            ':dayIndex' => $dayIndex === 0 ? 7 : $dayIndex,
            ':type'     => AbstractUser::USER_ROLE_PROVIDER
        ];

        try {
            $statement = $this->connection->prepare("SELECT
                u.id AS user_id,
                u.firstName AS user_firstName,
                u.lastName AS user_lastName,
                wdt.id AS weekDay_id,
                wdt.dayIndex AS weekDay_dayIndex,
                wdt.startTime AS weekDay_startTime,
                wdt.endTime As weekDay_endTime,
                tot.id AS timeOut_id,
                tot.startTime AS timeOut_startTime,
                tot.endTime AS timeOut_endTime
              FROM {$this->table} u
              LEFT JOIN {$this->providerWeekDayTable} wdt ON wdt.userId = u.id
              LEFT JOIN {$this->providerTimeOutTable} tot ON tot.weekDayId = wdt.id
              WHERE u.type = :type AND
              wdt.dayIndex = :dayIndex AND
              {$currentDateTime} >= wdt.startTime AND
              {$currentDateTime} <= wdt.endTime AND
              {$currentDateTime} >= tot.startTime AND
              {$currentDateTime} <= tot.endTime");

            $statement->execute($params);

            $rows = $statement->fetchAll();
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to find by id in ' . __CLASS__, $e->getCode(), $e);
        }

        $result = [];

        foreach ($rows as $row) {
            $result[$row['user_id']] = $row;
        }

        return $result;
    }

    /**
     * @return array
     * @throws QueryExecutionException
     */
    public function getOnVacation()
    {
        $currentDateTime = "STR_TO_DATE('" . DateTimeService::getNowDateTime() . "', '%Y-%m-%d %H:%i:%s')";

        $params = [
            ':type' => AbstractUser::USER_ROLE_PROVIDER
        ];

        try {
            $statement = $this->connection->prepare("SELECT
                u.id,
                u.firstName,
                u.lastName,
                dot.startDate,
                dot.endDate,
                dot.name
              FROM {$this->table} u
              LEFT JOIN {$this->providerDayOffTable} dot ON dot.userId = u.id
              WHERE u.type = :type AND
              DATE_FORMAT({$currentDateTime}, '%Y-%m-%d') BETWEEN dot.startDate AND dot.endDate");

            $statement->execute($params);

            $rows = $statement->fetchAll();
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to find by id in ' . __CLASS__, $e->getCode(), $e);
        }

        $result = [];

        foreach ($rows as $row) {
            $result[$row['id']] = $row;
        }

        return $result;
    }

    /**
     * Return an array of providers with the number of appointments for the given date period.
     * Keys of the array are Provider IDs.
     *
     * @param $criteria
     *
     * @return array
     * @throws InvalidArgumentException
     * @throws QueryExecutionException
     */
    public function getAllNumberOfAppointments($criteria)
    {
        $appointmentTable = AppointmentsTable::getTableName();

        $params = [];
        $where = [];

        if ($criteria['dates']) {
            $where[] = "(DATE_FORMAT(a.bookingStart, '%Y-%m-%d') BETWEEN :bookingFrom AND :bookingTo)";
            $params[':bookingFrom'] = DateTimeService::getCustomDateTimeInUtc($criteria['dates'][0]);
            $params[':bookingTo'] = DateTimeService::getCustomDateTimeInUtc($criteria['dates'][1]);
        }

        if (isset($criteria['status'])) {
            $where[] = 'u.status = :status';
            $params[':status'] = $criteria['status'];
        }

        $where = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        try {
            $statement = $this->connection->prepare("SELECT
                u.id,
                CONCAT(u.firstName, ' ', u.lastName) AS name,
                COUNT(a.providerId) AS appointments
            FROM {$this->table} u 
            INNER JOIN {$appointmentTable} a ON u.id = a.providerId
            $where
            GROUP BY providerId");

            $statement->execute($params);

            $rows = $statement->fetchAll();
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to get data from ' . __CLASS__, $e->getCode(), $e);
        }

        $result = [];

        foreach ($rows as $row) {
            $result[$row['id']] = $row;
        }

        return $result;
    }

    /**
     * Return an array of providers with the number of views for the given date period.
     * Keys of the array are Providers IDs.
     *
     * @param $criteria
     *
     * @return array
     * @throws QueryExecutionException
     */
    public function getAllNumberOfViews($criteria)
    {
        $params = [];
        $where = [];

        if ($criteria['dates']) {
            $where[] = "(DATE_FORMAT(pv.date, '%Y-%m-%d') BETWEEN :bookingFrom AND :bookingTo)";
            $params[':bookingFrom'] = DateTimeService::getCustomDateTimeInUtc($criteria['dates'][0]);
            $params[':bookingTo'] = DateTimeService::getCustomDateTimeInUtc($criteria['dates'][1]);
        }

        if (isset($criteria['status'])) {
            $where[] = 'u.status = :status';
            $params[':status'] = $criteria['status'];
        }

        $where = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        try {
            $statement = $this->connection->prepare("SELECT
            u.id,
            CONCAT(u.firstName, ' ', u.lastName) as name,
            SUM(pv.views) AS views
            FROM {$this->table} u
            INNER JOIN {$this->providerViewsTable} pv ON pv.userId = u.id 
            $where
            GROUP BY u.id");

            $statement->execute($params);

            $rows = $statement->fetchAll();
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to get data from ' . __CLASS__, $e->getCode(), $e);
        }

        $result = [];

        foreach ($rows as $row) {
            $result[$row['id']] = $row;
        }

        return $result;
    }

    /**
     * @param $providerId
     *
     * @return string
     * @throws QueryExecutionException
     */
    public function addViewStats($providerId)
    {
        $date = DateTimeService::getNowDate();

        $params = [
            ':userId' => $providerId,
            ':date'   => $date,
            ':views'  => 1
        ];

        try {
            // Check if there is already data for this provider for this date
            $statement = $this->connection->prepare(
                "SELECT COUNT(*) AS count 
                FROM {$this->providerViewsTable} AS pv 
                WHERE pv.userId = :userId 
                AND pv.date = :date"
            );

            $statement->bindParam(':userId', $providerId);
            $statement->bindParam(':date', $date);
            $statement->execute();
            $count = $statement->fetch()['count'];

            if (!$count) {
                $statement = $this->connection->prepare(
                    "INSERT INTO {$this->providerViewsTable}
                    (`userId`, `date`, `views`)
                    VALUES 
                    (:userId, :date, :views)"
                );
            } else {
                $statement = $this->connection->prepare(
                    "UPDATE {$this->providerViewsTable} pv SET pv.views = pv.views + :views
                    WHERE pv.userId = :userId
                    AND pv.date = :date"
                );
            }

            $response = $statement->execute($params);
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to add data in ' . __CLASS__, $e->getCode(), $e);
        }

        if (!$response) {
            throw new QueryExecutionException('Unable to add data in ' . __CLASS__);
        }

        return true;
    }

    /**
     *
     * @return array
     * @throws QueryExecutionException
     */
    public function getProvidersServices()
    {
        try {
            $statement = $this->connection->prepare(
                "SELECT
                    u.id AS user_id,
                    st.serviceId AS service_id,
                    st.price AS service_price,
                    st.minCapacity AS service_minCapacity,
                    st.maxCapacity AS service_maxCapacity
                FROM {$this->table} u
                INNER JOIN {$this->providerServicesTable} st ON st.userId = u.id
                WHERE u.type = :type
                ORDER BY CONCAT(u.firstName, ' ', u.lastName)"
            );

            $type = AbstractUser::USER_ROLE_PROVIDER;

            $statement->bindParam(':type', $type);

            $statement->execute();

            $rows = $statement->fetchAll();
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to find by id in ' . __CLASS__, $e->getCode(), $e);
        }

        $result = [];

        foreach ($rows as $row) {
            $userId = (int)$row['user_id'];
            $serviceId = (int)$row['service_id'];

            if (!array_key_exists($userId, $result) || !array_key_exists($serviceId, $result[$userId])) {
                $result[$userId][$serviceId] = [
                    'price'       => $row['service_price'],
                    'minCapacity' => (int)$row['service_minCapacity'],
                    'maxCapacity' => (int)$row['service_maxCapacity']
                ];
            }
        }

        return $result;
    }

    /** @noinspection MoreThanThreeArgumentsInspection */
    /**
     * @param array $row
     * @param array $providerRows
     * @param array $serviceRows
     * @param array $providerServiceRows
     *
     * @return void
     */
    private function parseUserRow($row, &$providerRows, &$serviceRows, &$providerServiceRows)
    {
        $userId = (int)$row['user_id'];
        $serviceId = isset($row['service_id']) ? (int)$row['service_id'] : null;
        $extraId = isset($row['extra_id']) ? $row['extra_id'] : null;
        $couponId = isset($row['coupon_id']) ? $row['coupon_id'] : null;
        $googleCalendarId = isset($row['google_calendar_id']) ? $row['google_calendar_id'] : null;
        $outlookCalendarId = isset($row['outlook_calendar_id']) ? $row['outlook_calendar_id'] : null;
        $weekDayId = isset($row['weekDay_id']) ? $row['weekDay_id'] : null;
        $timeOutId = isset($row['timeOut_id']) ? $row['timeOut_id'] : null;
        $periodId = isset($row['period_id']) ? $row['period_id'] : null;
        $periodServiceId = isset($row['periodService_id']) ? $row['periodService_id'] : null;
        $specialDayId = isset($row['specialDay_id']) ? $row['specialDay_id'] : null;
        $specialDayPeriodId = isset($row['specialDayPeriod_id']) ? $row['specialDayPeriod_id'] : null;
        $specialDayPeriodServiceId = isset($row['specialDayPeriodService_id']) ? $row['specialDayPeriodService_id'] : null;
        $dayOffId = isset($row['dayOff_id']) ? $row['dayOff_id'] : null;

        if (!array_key_exists($userId, $providerRows)) {
            $providerRows[$userId] = [
                'id'               => $userId,
                'type'             => 'provider',
                'status'           => isset($row['user_status']) ? $row['user_status'] : null,
                'externalId'       => isset($row['external_id']) ? $row['external_id'] : null,
                'firstName'        => $row['user_firstName'],
                'lastName'         => $row['user_lastName'],
                'email'            => $row['user_email'],
                'note'             => isset($row['note']) ? $row['note'] : null,
                'phone'            => isset($row['phone']) ? $row['phone'] : null,
                'zoomUserId'       => isset($row['user_zoom_user_id']) ? $row['user_zoom_user_id'] : null,
                'countryPhoneIso'  => isset($row['user_countryPhoneIso']) ? $row['user_countryPhoneIso'] : null,
                'locationId'       => isset($row['user_locationId']) ? $row['user_locationId'] : null,
                'pictureFullPath'  => isset($row['picture_full_path']) ? $row['picture_full_path'] : null,
                'pictureThumbPath' => isset($row['picture_thumb_path']) ? $row['picture_thumb_path'] : null,
                'googleCalendar'   => [],
                'weekDayList'      => [],
                'dayOffList'       => [],
                'specialDayList'   => [],
                'serviceList'      => [],
            ];
        }

        if ($googleCalendarId &&
            array_key_exists($userId, $providerRows) &&
            empty($providerRows[$userId]['googleCalendar'])
        ) {
            $providerRows[$userId]['googleCalendar']['id'] = $row['google_calendar_id'];
            $providerRows[$userId]['googleCalendar']['token'] = $row['google_calendar_token'];
            $providerRows[$userId]['googleCalendar']['calendarId'] = isset($row['google_calendar_calendar_id']) ? $row['google_calendar_calendar_id'] : null;
        }

        if ($outlookCalendarId &&
            array_key_exists($userId, $providerRows) &&
            empty($providerRows[$userId]['outlookCalendar'])
        ) {
            $providerRows[$userId]['outlookCalendar']['id'] = $row['outlook_calendar_id'];
            $providerRows[$userId]['outlookCalendar']['token'] = $row['outlook_calendar_token'];
            $providerRows[$userId]['outlookCalendar']['calendarId'] = isset($row['outlook_calendar_calendar_id']) ? $row['outlook_calendar_calendar_id'] : null;
        }

        if ($weekDayId &&
            array_key_exists($userId, $providerRows) &&
            !array_key_exists($weekDayId, $providerRows[$userId]['weekDayList'])
        ) {
            $providerRows[$userId]['weekDayList'][$weekDayId] = [
                'id'          => $weekDayId,
                'dayIndex'    => $row['weekDay_dayIndex'],
                'startTime'   => $row['weekDay_startTime'],
                'endTime'     => $row['weekDay_endTime'],
                'timeOutList' => [],
                'periodList'  => [],
            ];
        }

        if ($periodId &&
            $weekDayId &&
            array_key_exists($userId, $providerRows) &&
            array_key_exists($weekDayId, $providerRows[$userId]['weekDayList']) &&
            !array_key_exists($periodId, $providerRows[$userId]['weekDayList'][$weekDayId]['periodList'])
        ) {
            $providerRows[$userId]['weekDayList'][$weekDayId]['periodList'][$periodId] = [
                'id'                => $periodId,
                'startTime'         => $row['period_startTime'],
                'endTime'           => $row['period_endTime'],
                'locationId'        => $row['period_locationId'],
                'periodServiceList' => [],
            ];
        }

        if ($periodServiceId &&
            $periodId &&
            $weekDayId &&
            array_key_exists($userId, $providerRows) &&
            array_key_exists($weekDayId, $providerRows[$userId]['weekDayList']) &&
            array_key_exists($periodId, $providerRows[$userId]['weekDayList'][$weekDayId]['periodList']) &&
            !array_key_exists($periodServiceId, $providerRows[$userId]['weekDayList'][$weekDayId]['periodList'][$periodId]['periodServiceList'])
        ) {
            $providerRows[$userId]['weekDayList'][$weekDayId]['periodList'][$periodId]['periodServiceList'][$periodServiceId] = [
                'id'        => $periodServiceId,
                'serviceId' => $row['periodService_serviceId'],
            ];
        }

        if ($timeOutId &&
            $weekDayId &&
            array_key_exists($userId, $providerRows) &&
            array_key_exists($weekDayId, $providerRows[$userId]['weekDayList']) &&
            !array_key_exists($timeOutId, $providerRows[$userId]['weekDayList'][$weekDayId]['timeOutList'])
        ) {
            $providerRows[$userId]['weekDayList'][$weekDayId]['timeOutList'][$timeOutId] = [
                'id'        => $timeOutId,
                'startTime' => $row['timeOut_startTime'],
                'endTime'   => $row['timeOut_endTime'],
            ];
        }

        if ($specialDayId &&
            array_key_exists($userId, $providerRows) &&
            !array_key_exists($specialDayId, $providerRows[$userId]['specialDayList'])
        ) {
            $providerRows[$userId]['specialDayList'][$specialDayId] = [
                'id'         => $specialDayId,
                'startDate'  => $row['specialDay_startDate'],
                'endDate'    => $row['specialDay_endDate'],
                'periodList' => [],
            ];
        }

        if ($specialDayPeriodId &&
            $specialDayId &&
            array_key_exists($userId, $providerRows) &&
            array_key_exists($specialDayId, $providerRows[$userId]['specialDayList']) &&
            !array_key_exists($specialDayPeriodId, $providerRows[$userId]['specialDayList'][$specialDayId]['periodList'])
        ) {
            $providerRows[$userId]['specialDayList'][$specialDayId]['periodList'][$specialDayPeriodId] = [
                'id'                => $specialDayPeriodId,
                'startTime'         => $row['specialDayPeriod_startTime'],
                'endTime'           => $row['specialDayPeriod_endTime'],
                'locationId'        => $row['specialDayPeriod_locationId'],
                'periodServiceList' => [],
            ];
        }

        if ($specialDayPeriodServiceId &&
            $specialDayPeriodId &&
            $specialDayId &&
            array_key_exists($userId, $providerRows) &&
            array_key_exists($specialDayId, $providerRows[$userId]['specialDayList']) &&
            array_key_exists($specialDayPeriodId, $providerRows[$userId]['specialDayList'][$specialDayId]['periodList']) &&
            !array_key_exists($specialDayPeriodServiceId, $providerRows[$userId]['specialDayList'][$specialDayId]['periodList'][$specialDayPeriodId]['periodServiceList'])
        ) {
            $providerRows[$userId]['specialDayList'][$specialDayId]['periodList'][$specialDayPeriodId]['periodServiceList'][$specialDayPeriodServiceId] = [
                'id'        => $specialDayPeriodServiceId,
                'serviceId' => $row['specialDayPeriodService_serviceId'],
            ];
        }

        if ($dayOffId &&
            array_key_exists($userId, $providerRows) &&
            !array_key_exists($dayOffId, $providerRows[$userId]['dayOffList'])
        ) {
            $providerRows[$userId]['dayOffList'][$dayOffId] = [
                'id'        => $dayOffId,
                'name'      => $row['dayOff_name'],
                'startDate' => $row['dayOff_startDate'],
                'endDate'   => $row['dayOff_endDate'],
                'repeat'    => $row['dayOff_repeat'],
            ];
        }

        if ($serviceId &&
            !array_key_exists($serviceId, $serviceRows)
        ) {
            $serviceRows[$serviceId] = [
                'id'               => $serviceId,
                'price'            => $row['service_price'],
                'minCapacity'      => $row['service_minCapacity'],
                'maxCapacity'      => $row['service_maxCapacity'],
                'name'             => $row['service_name'],
                'description'      => $row['service_description'],
                'color'            => $row['service_color'],
                'status'           => $row['service_status'],
                'categoryId'       => (int)$row['service_categoryId'],
                'duration'         => $row['service_duration'],
                'bringingAnyone'   => $row['service_bringingAnyone'],
                'show'             => isset($row['service_show']) ? $row['service_show'] : null,
                'aggregatedPrice'  => isset($row['service_aggregatedPrice']) ? $row['service_aggregatedPrice'] : null,
                'pictureFullPath'  => isset($row['service_picture_full']) ? $row['service_picture_full'] : null,
                'pictureThumbPath' => isset($row['service_picture_thumb']) ? $row['service_picture_thumb'] : null,
                'timeBefore'       => isset($row['service_timeBefore']) ? $row['service_timeBefore'] : null,
                'timeAfter'        => isset($row['service_timeAfter']) ? $row['service_timeAfter'] : null,
                'extras'           => [],
                'coupons'          => [],
                'settings'         => isset($row['service_settings']) ? $row['service_settings'] : null,
                'recurringCycle'   => isset($row['service_recurringCycle']) ? $row['service_recurringCycle'] : null,
                'recurringSub'     => isset($row['service_recurringSub']) ? $row['service_recurringSub'] : null,
                'recurringPayment' => isset($row['service_recurringPayment']) ? $row['service_recurringPayment'] : null,
                'translations'     => isset($row['service_translations']) ? $row['service_translations'] : null,
                'deposit'          => isset($row['service_deposit']) ? $row['service_deposit'] : null,
                'depositPayment'   => isset($row['service_depositPayment']) ? $row['service_depositPayment'] : null,
                'depositPerPerson' => isset($row['service_depositPerPerson']) ? $row['service_depositPerPerson'] : null,
            ];
        }

        if ($extraId &&
            $serviceId &&
            array_key_exists($serviceId, $serviceRows) &&
            !array_key_exists($extraId, $serviceRows[$serviceId]['extras'])
        ) {
            $serviceRows[$serviceId]['extras'][$extraId] = [
                'id'              => $extraId,
                'name'            => $row['extra_name'],
                'price'           => $row['extra_price'],
                'maxQuantity'     => $row['extra_maxQuantity'],
                'position'        => $row['extra_position'],
                'aggregatedPrice' => $row['extra_aggregatedPrice'],
                'description'     => $row['extra_description']
            ];
        }

        if ($couponId &&
            $serviceId &&
            array_key_exists($serviceId, $serviceRows) &&
            !array_key_exists($couponId, $serviceRows[$serviceId]['coupons'])
        ) {
            $serviceRows[$serviceId]['coupons'][$couponId] = [
                'id'            => $couponId,
                'code'          => $row['coupon_code'],
                'discount'      => $row['coupon_discount'],
                'deduction'     => $row['coupon_deduction'],
                'limit'         => $row['coupon_limit'],
                'customerLimit' => $row['coupon_customerLimit'],
                'status'        => $row['coupon_status']
            ];
        }

        if ($serviceId && (!array_key_exists($userId, $providerServiceRows) || !array_key_exists($serviceId, $providerServiceRows[$userId]))) {
            $providerServiceRows[$userId][$serviceId] = [
                'price'       => $row['service_price'],
                'minCapacity' => (int)$row['service_minCapacity'],
                'maxCapacity' => (int)$row['service_maxCapacity']
            ];
        }
    }

    /**
     * @param int $userId
     *
     * @return mixed
     * @throws QueryExecutionException
     */
    public function deleteViewStats($userId)
    {
        $params = [
            ':userId' => $userId,
        ];

        try {
            $statement = $this->connection->prepare(
                "DELETE FROM {$this->providerViewsTable} WHERE userId = :userId"
            );

            return $statement->execute($params);
        } catch (\Exception $e) {
            throw new QueryExecutionException('Unable to delete data from ' . __CLASS__, $e->getCode(), $e);
        }
    }
}
