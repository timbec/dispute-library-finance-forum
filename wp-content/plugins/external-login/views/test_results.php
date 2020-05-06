<?php if ($exlog_test_results_data) : ?>

<table>
    <thead>
        <tr>
            <?php foreach ($exlog_test_results_data[0] as $key => $value) :?>
                <th><?php echo $key; ?></th>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($exlog_test_results_data as $user) :?>
            <tr>
            <?php foreach ($user as $key => $value) :?>
                <td><?php echo substr($value, 0, 10); ?></td>
            <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php else : ?>
    <?php throw new Exception("External Login Error: No test results found."); ?>
<?php endif; ?>
