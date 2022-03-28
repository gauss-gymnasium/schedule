<?php

/**
 * Outputs the HTML representation of a Schedule named `$schedule`.
 * Usage: Insert `include 'schedule_html.php'` wherever a schedule should be displayed.
 * Requires `$schedule` to be set before this file is included.
 */

if (!defined('INCLUDE_ROOT')) {
    exit('Kein direkter Zugriff erlaubt.');
}

include_once 'schedule.php';

if (!isset($schedule) || !($schedule instanceof Schedule)) {
    $schedule_type = is_object($schedule) ? get_class($schedule) : gettype($schedule);
    throw new Exception('$schedule is not defined or not of type Schedule: ' . $schedule_type);
}

// This is a type hint.
// It tells the IDE that `$schedule` is of class `Schedule`.
// This gives us stuff like auto-completion and documentation on hover.
/** @var \Schedule $schedule */

$schedule_id = 'schedule-' . $schedule->get_timestamp();

?>

<div class="accordion" id="<?php echo $schedule_id ?>">
    <?php if (count($schedule->get_infos()) > 0) { ?>
        <div class="accordion-item">
            <h2 class="accordion-header" id="<?php echo $schedule_id ?>-header-general">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#<?php echo $schedule_id ?>-collapse-general" aria-expanded="false" aria-controls="<?php echo $schedule_id ?>-collapse-general">
                    Allgemein
                    <?php if (in_array('new-changes', $schedule->get_info_tag_classes())) { ?>
                        <span class="badge period-badge period-badge-small new-changes">Plan geändert</span>
                    <?php } ?>
                </button>
            </h2>
            <div id="<?php echo $schedule_id ?>-collapse-general" class="accordion-collapse collapse" aria-labelledby="<?php echo $schedule_id ?>-header-general" data-bs-parent="#<?php echo $schedule_id ?>">
                <div class="accordion-body">
                    <ul class="lesson-list">
                        <?php foreach ($schedule->get_infos() as $info) { ?>
                            <li class="lesson-list-item <?php echo $info->get_formatted_tag_classes() ?>">
                                <span class="badge period-badge" aria-label="<?php in_array("new-changes", $info->get_tag_classes()) && print "Neue Information:" ?>">info</span>
                                <?php echo $info->get_info() ?>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </div>
    <?php
    }
    foreach ($schedule->get_grades() as $grade_id => $grade) {
        $header_id = $schedule_id . '-heading-' . $grade_id;
        $collapse_id = $schedule_id . '-collapse-' . $grade_id;
    ?>
        <div class="accordion-item">
            <h2 class="accordion-header" id="<?php echo $header_id ?>">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#<?php echo $collapse_id ?>" aria-expanded="false" aria-controls="<?php echo $collapse_id ?>">
                    Klasse <?php echo $grade->get_name() ?>
                    <?php if (in_array('new-changes', $grade->get_tag_classes())) { ?>
                        <span class="badge period-badge period-badge-small new-changes">Plan geändert</span>
                    <?php } ?>
                </button>
            </h2>
            <div id="<?php echo $collapse_id ?>" class="accordion-collapse collapse" aria-labelledby="<?php echo $header_id ?>" data-bs-parent="#<?php echo $schedule_id ?>">
                <div class="accordion-body">
                    <?php foreach ($grade->get_groups() as $group) { ?>
                        <div class="card align-top">
                            <div class="card-header">
                                <?php echo $group->get_teacher() == '' ? 'Gesamte Klasse' : $group->get_teacher() ?>
                            </div>
                            <div class="card-body">
                                <ul class="lesson-list">
                                    <?php foreach ($group->get_lessons() as $lesson) { ?>
                                        <li class="lesson-list-item <?php echo $lesson->get_formatted_tag_classes() ?>">
                                            <span class="badge period-badge" aria-label="<?php in_array("new-changes", $lesson->get_tag_classes()) && print "Neue Änderung: " ?><?php echo $lesson->get_period() ?>. Stunde"><?php echo $lesson->get_period() ?>.</span>
                                            <?php echo $lesson->get_info() ?>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    <?php } ?>
</div>