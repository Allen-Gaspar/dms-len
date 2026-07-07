<?php
class Pagination {
    public static function render(int $total, int $offset, int $limit, string $baseUrl, array $params = []): string {
        if ($total <= $limit) {
            return '';
        }
        $paramsStr = '';
        foreach ($params as $k => $v) {
            if ($v !== '' && $k !== 'offset') {
                $paramsStr .= '&' . urlencode($k) . '=' . urlencode($v);
            }
        }
        $from = $total > 0 ? $offset + 1 : 0;
        $to   = min($total, $offset + $limit);
        $prev = $offset > 0
            ? '<a href="' . htmlspecialchars($baseUrl . '?offset=' . max(0, $offset - $limit) . $paramsStr) . '" class="btn btn-outline">&larr; Newer Entries</a>'
            : '<button class="btn btn-outline" disabled>&larr; Newer Entries</button>';
        $next = ($offset + $limit) < $total
            ? '<a href="' . htmlspecialchars($baseUrl . '?offset=' . ($offset + $limit) . $paramsStr) . '" class="btn btn-outline">Older Entries &rarr;</a>'
            : '<button class="btn btn-outline" disabled>Older Entries &rarr;</button>';

        return '<div class="pagination">' . $prev .
            '<span class="pagination-info">Showing rows ' . $from . ' to ' . $to . ' of ' . $total . '</span>' .
            $next . '</div>';
    }
}
