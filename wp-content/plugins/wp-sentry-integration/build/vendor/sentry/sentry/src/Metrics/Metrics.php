<?php

declare (strict_types=1);
namespace Sentry\Metrics;

use Sentry\EventId;
use Sentry\Metrics\Types\CounterType;
use Sentry\Metrics\Types\DistributionType;
use Sentry\Metrics\Types\GaugeType;
use Sentry\Metrics\Types\SetType;
final class Metrics
{
    /**
     * @var self|null
     */
    private static $instance;
    /**
     * @var MetricsAggregator
     */
    private $aggregator;
    private function __construct()
    {
        $this->aggregator = new \Sentry\Metrics\MetricsAggregator();
    }
    public static function getInstance() : self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    /**
     * @param int|float $value
     * @param string[]  $tags
     */
    public function increment(string $key, $value, ?\Sentry\Metrics\MetricsUnit $unit = null, array $tags = [], ?int $timestamp = null, int $stackLevel = 0) : void
    {
        $this->aggregator->add(\Sentry\Metrics\Types\CounterType::TYPE, $key, $value, $unit, $tags, $timestamp, $stackLevel);
    }
    /**
     * @param int|float $value
     * @param string[]  $tags
     */
    public function distribution(string $key, $value, ?\Sentry\Metrics\MetricsUnit $unit = null, array $tags = [], ?int $timestamp = null, int $stackLevel = 0) : void
    {
        $this->aggregator->add(\Sentry\Metrics\Types\DistributionType::TYPE, $key, $value, $unit, $tags, $timestamp, $stackLevel);
    }
    /**
     * @param int|float $value
     * @param string[]  $tags
     */
    public function gauge(string $key, $value, ?\Sentry\Metrics\MetricsUnit $unit = null, array $tags = [], ?int $timestamp = null, int $stackLevel = 0) : void
    {
        $this->aggregator->add(\Sentry\Metrics\Types\GaugeType::TYPE, $key, $value, $unit, $tags, $timestamp, $stackLevel);
    }
    /**
     * @param int|string $value
     * @param string[]   $tags
     */
    public function set(string $key, $value, ?\Sentry\Metrics\MetricsUnit $unit = null, array $tags = [], ?int $timestamp = null, int $stackLevel = 0) : void
    {
        $this->aggregator->add(\Sentry\Metrics\Types\SetType::TYPE, $key, $value, $unit, $tags, $timestamp, $stackLevel);
    }
    public function flush() : ?\Sentry\EventId
    {
        return $this->aggregator->flush();
    }
}
