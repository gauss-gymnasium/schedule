<?php

if (!defined('INCLUDE_ROOT')) {
    exit('Kein direkter Zugriff erlaubt.');
}

class Schedule implements JsonSerializable
{

    private $timestamp = 0;
    private $grades = [];
    public $infos = [];


    function __construct(int $timestamp)
    {
        $this->timestamp = $timestamp;
    }

    /**
     * @return string Date of schedule in the format `DD.MM.YYYY`.
     */
    function get_date()
    {
        return date("d.m.Y", $this->timestamp);
    }

    /**
     * @return timestamp Timestamp of schedule
     */
    function get_timestamp()
    {
        return date($this->timestamp);
    }

    /**
     * @return Grade[] Grades of schedule
     */
    function get_grades()
    {
        return $this->grades;
    }

    /**
     * @return Lesson[] General information items of schedule
     */
    function get_infos()
    {
        return $this->infos;
    }

    /**
     * @return Tag[] Tags associated to general information items
     */
    function get_info_tags()
    {
        $tags = [];
        foreach ($this->get_infos() as $info) {
            array_push($tags, ...$info->get_tags());
        }
        return $tags;
    }

    /**
     * @return string[] Unique classes associated to general informations' tags
     */
    function get_info_tag_classes()
    {
        $tag_classes = [];
        foreach ($this->get_infos() as $info) {
            array_push($tag_classes, ...$info->get_tag_classes());
        }
        return array_unique($tag_classes);
    }

    /**
     * Add group to given grade in schedule.
     * 
     * @param string $grade_name Name of grade to add group to, e.g. `Kl. 6`
     * @param Group $group Group to add
     */
    function add_group(string $grade_name, Group $group)
    {
        foreach ($this->grades as $grade) {
            if ($grade->get_name() == $grade_name) {
                $grade->add_group($group);
                return;
            }
        }
        $this->add_grade($grade_name)->add_group($group);
    }

    /**
     * Add grade with name to schedule.
     * 
     * @param string $grade_name Name of grade to add, e.g. `Kl. 6`
     * @return Grade Newly added grade
     */
    function add_grade(string $grade_name): Grade
    {
        $new_grade = new Grade($grade_name);
        array_push($this->grades, $new_grade);
        return $new_grade;
    }

    /**
     * Add general information to schedule.
     * 
     * @param string $general_info General information to add
     */
    function add_info(string $general_info)
    {
        $general_info = new Lesson(null, $general_info);
        array_push($this->infos, $general_info);
    }

    function __toString()
    {
        $infos = '';
        foreach ($this->get_infos() as $info) {
            if ($infos != '') $infos .= ', ';
            $infos .= $info;
        }

        $grades = '';
        foreach ($this->get_grades() as $grade) {
            if ($grades != '') $grades .= ', ';
            $grades .= $grade;
        }

        return "Schedule ( [" . $this->get_date() . "] " . $infos . " | " . $grades . " )";
    }

    function jsonSerialize()
    {
        // Since the attributes are private, we need to manually 
        //   create a serialzable object of this class instance
        return [
            'date' => $this->get_date(),
            'generalInfo' => $this->get_infos(),
            'generalInfoTags' => $this->get_info_tags(),
            'grades' => $this->get_grades(),
        ];
    }
}

class Grade implements JsonSerializable
{

    protected $name = '';
    private $groups = [];

    function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string Name of grade, e.g. `6. Kl.`
     */
    function get_name()
    {
        return $this->name;
    }

    /**
     * @return Group[] Groups of this grade
     */
    function get_groups()
    {
        return $this->groups;
    }

    /**
     * @return Tag[] Tags in this grade's lessons
     */
    function get_tags()
    {
        $tags = [];
        foreach ($this->get_groups() as $group) {
            foreach ($group->get_lessons() as $lesson) {
                array_push($tags, ...$lesson->get_tags());
            }
        }
        return $tags;
    }

    /**
     * @return string[] Unique tag strings associated to all tags of this grade's lessons
     */
    function get_tag_strings()
    {
        $fn = function (Tag $tag): string {
            return $tag->get_tag_string();
        };
        return array_unique(
            array_map($fn, $this->get_tags())
        );
    }

    /**
     * @return string[] Unique classes associated to all tags of this grade's lessons
     */
    function get_tag_classes()
    {
        $fn = function (Tag $tag): string {
            return $tag->get_class();
        };
        return array_unique(
            array_map($fn, $this->get_tags())
        );
    }

    /**
     * @return string String of unique tag CSS classes in this grade, ready to be inserted into class attribute
     */
    function get_formatted_tag_classes(): string
    {
        return implode(" ", $this->get_tag_classes());
    }

    function add_group(Group $group)
    {
        array_push($this->groups, $group);
    }

    function __toString()
    {
        $groups = '';
        foreach ($this->get_groups() as $group) {
            if ($groups != '') $groups .= ', ';
            $groups .= $group;
        }

        return "Grade ( [Cl. " . $this->get_name() . "] " . $groups . " )";
    }

    function jsonSerialize()
    {
        // Since the attributes are private, we need to manually 
        //   create a serialzable object of this class instance
        return [
            'name' => $this->get_name(),
            'groups' => $this->get_groups(),
            'tags' => $this->get_tags(),
        ];
    }
}

class Group implements JsonSerializable
{

    protected $teacher = '';
    private $lessons = [];

    function __construct(string $teacher)
    {
        $this->teacher = $teacher;
    }

    /**
     * @return string Teacher of group. May be empty
     */
    function get_teacher()
    {
        return $this->teacher;
    }

    /**
     * @return Lesson[] Lessons of group
     */
    function get_lessons()
    {
        return $this->lessons;
    }

    /**
     * Add lesson to group.
     * 
     * @param int $period Period of lesson
     * @param string $info Information for this group's lesson
     * @return Lesson Newly added lesson
     */
    function add_lesson(int $period, string $info): Lesson
    {
        $lesson = new Lesson($period, $info);
        array_push($this->lessons, $lesson);
        return $lesson;
    }

    function __toString()
    {
        $lessons = '';
        foreach ($this->get_lessons() as $lesson) {
            if ($lessons != '') $lessons .= ', ';
            $lessons .= $lesson;
        }

        $teacher = $this->get_teacher() == '' ? 'Whole Class' : $this->get_teacher();

        return "Group ( [" . $teacher . "] " . $lessons . " )";
    }

    function jsonSerialize()
    {
        // Since the attributes are private, we need to manually 
        //   create a serialzable object of this class instance
        return [
            'teacher' => $this->get_teacher(),
            'lessons' => $this->get_lessons(),
        ];
    }
}


class Lesson implements JsonSerializable
{

    protected $period = null;
    private $info = '';
    private $tags = [];

    /**
     * @param ?int period Period of this lesson, e.g. `5`. Period will not be displayed if null.
     */
    public function __construct(?int $period, $info)
    {
        $this->period = $period;
        $this->info = $this->strip_tags($info);
        $this->tags = $this->extract_tags($info);
    }

    /**
     * @return ?int Period of this lesson
     */
    function get_period(): ?int
    {
        return $this->period;
    }

    /**
     * @return string Information for this lesson, free of tags
     */
    function get_info(): string
    {
        return $this->info;
    }

    /**
     * @return Tag[] Unique tags for this lesson
     */
    function get_tags()
    {
        return $this->tags;
    }

    /**
     * @return string[] Unique tag strings of this lesson's tags
     */
    function get_tag_strings()
    {
        $fn = function (Tag $tag): string {
            return $tag->get_tag_string();
        };
        return array_map($fn, $this->get_tags());
    }

    /**
     * @return string[] Unique classes associated to this lesson's tags
     */
    function get_tag_classes()
    {
        $fn = function (Tag $tag): string {
            return $tag->get_class();
        };
        return array_unique(
            array_map($fn, $this->get_tags())
        );
    }

    /**
     * @return string String of unique tag CSS classes in this lesson, ready to be inserted into class attribute
     */
    function get_formatted_tag_classes(): string
    {
        return implode(" ", $this->get_tag_classes());
    }

    /**
     * Remove all tags from $info.
     * 
     * @param string $info Lesson information to remove tags from
     * @return string $info free of tags
     */
    private function strip_tags(string $info): string
    {
        $fn = function (Tag $tag): string {
            return $tag->get_tag_string();
        };
        $tag_strings = array_map($fn, $GLOBALS['tags']);
        return str_replace($tag_strings, '', html_entity_decode($info));
    }

    /**
     * Returns list of tags in $info.
     * 
     * @param string $info Lesson information to return tags for
     * @return Tag[] Array of unique tags within $info
     */
    private function extract_tags($info)
    {
        $found_tags = [];
        $decoded_info = html_entity_decode($info);
        foreach ($GLOBALS['tags'] as $tag) {
            if (stripos($decoded_info, $tag->get_tag_string()) !== false) {
                array_push($found_tags, $tag);
            }
        }
        return $found_tags;
    }

    function __toString()
    {
        $tags = '';
        foreach ($this->get_tag_strings() as $tag_string) {
            if ($tags != '') $tags .= ', ';
            $tags .= $tag_string;
        }

        $period = $this->get_period() == null ? 'General' : $this->get_period() . '.';

        return "Lesson ( [" . $period . "] " . $this->get_info() . " (" . $tags . ") )";
    }

    function jsonSerialize()
    {
        // Since the attributes are private, we need to manually 
        //   create a serialzable object of this class instance
        return [
            'period' => $this->get_period(),
            'info' => $this->get_info(),
            'tags' => $this->get_tags(),
        ];
    }
}

class Tag implements JsonSerializable
{
    private $tag_string;
    private $css_class;

    /**
     * Creates a Tag.
     * A Tag is a string that is mapped to a CSS class.
     * It can be added to a Lesson (i.e. inserted into the respective schedule table cell) 
     * which may influene the appearance or behaviour of this lesson.
     * 
     * Example: Inserting `&neu;` into a schedule table cell adds a "Änderung" badge to the group and highlights the lesson.
     * 
     * @param string $tag_string The string that needs to be inserted into the schedule table cell, e.g. `&neu;`
     * @param string $css_class The name of the CSS class this tag should be mapped to, e.g. `new-changes`
     */
    function __construct(string $tag_string, string $css_class)
    {
        $this->tag_string = $tag_string;
        $this->css_class = $css_class;
    }

    /**
     * @return string The tag string, e.g. `&neu;`
     */
    function get_tag_string(): string
    {
        return $this->tag_string;
    }

    /**
     * @return string The name of the CSS class this tag should be mapped to, e.g. `new-changes`
     */
    function get_class(): string
    {
        return $this->css_class;
    }

    function jsonSerialize()
    {
        // Since the attributes are private, we need to manually 
        //   create a serialzable object of this class instance
        return $this->get_tag_string();
    }
}
