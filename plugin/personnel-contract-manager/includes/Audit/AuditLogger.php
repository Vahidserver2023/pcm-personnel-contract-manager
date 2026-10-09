<?php
namespace PCM\Audit;

if (!defined('ABSPATH')) {
    exit;
}

final class AuditLogger
{
    public static function get_logs(array $filters = []): array
    {
        global $wpdb;
        $table = $wpdb->prefix . 'pcm_audit_logs';

        $query = "SELECT * FROM {$table} WHERE 1=1";
        $params = [];

        if (!empty($filters['action'])) {
            $query .= " AND action = %s";
            $params[] = $filters['action'];
        }

        if (!empty($filters['entity'])) {
            $query .= " AND entity = %s";
            $params[] = $filters['entity'];
        }

        if (!empty($filters['user_id'])) {
            $query .= " AND user_id = %d";
            $params[] = $filters['user_id'];
        }

        if (!empty($filters['record_id'])) {
            $query .= " AND record_id = %d";
            $params[] = $filters['record_id'];
        }

        if (!empty($filters['date_from'])) {
            $query .= " AND log_date >= %s";
            $params[] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $query .= " AND log_date <= %s";
            $params[] = $filters['date_to'];
        }

        $query .= " ORDER BY log_date DESC LIMIT 1000";

        if (!empty($params)) {
            $results = $wpdb->get_results($wpdb->prepare($query, ...$params), ARRAY_A);
        } else {
            $results = $wpdb->get_results($query, ARRAY_A);
        }

        return $results ?: [];
    }

    public static function get_entity_history(string $entity, int $record_id): array
    {
        global $wpdb;
        $table = $wpdb->prefix . 'pcm_audit_logs';

        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$table} WHERE entity = %s AND record_id = %d ORDER BY log_date ASC",
                $entity,
                $record_id
            ),
            ARRAY_A
        ) ?: [];
    }

    public static function get_user_activity(int $user_id, int $days = 30): array
    {
        global $wpdb;
        $table = $wpdb->prefix . 'pcm_audit_logs';
        $date = date('Y-m-d', strtotime("-{$days} days"));

        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$table} WHERE user_id = %d AND log_date >= %s ORDER BY log_date DESC",
                $user_id,
                $date
            ),
            ARRAY_A
        ) ?: [];
    }
}
