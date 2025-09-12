<?php

namespace App\Helpers;

class SMKSubjectHelper
{
    private static $config = null;

    /**
     * Load SMK subjects configuration
     */
    private static function loadConfig()
    {
        if (self::$config === null) {
            $configPath = config_path('smk_subjects.json');
            if (file_exists($configPath)) {
                self::$config = json_decode(file_get_contents($configPath), true);
            } else {
                self::$config = [];
            }
        }
        return self::$config;
    }

    /**
     * Get mandatory subjects for SMK
     */
    public static function getMandatorySubjects()
    {
        $config = self::loadConfig();
        return $config['smk_subjects']['mandatory'] ?? [
            'Bahasa Indonesia',
            'Bahasa Inggris',
            'Matematika'
        ];
    }

    /**
     * Get PKK subject for SMK
     */
    public static function getPKKSubject()
    {
        $config = self::loadConfig();
        return $config['smk_subjects']['pkk_subject'] ?? 'Produk/Projek Kreatif dan Kewirausahaan';
    }

    /**
     * Get available optional subjects for SMK
     */
    public static function getOptionalSubjects()
    {
        $config = self::loadConfig();
        return $config['smk_subjects']['optional_subjects'] ?? [];
    }

    /**
     * Get subjects for specific SMK major
     * SMK always gets: 1 PKK + 1 optional subject = 2 total
     */
    public static function getSubjectsForMajor($majorName)
    {
        $config = self::loadConfig();
        $rules = $config['smk_subjects']['mapping_rules'] ?? [];
        
        // Check for special cases first
        $specialCases = $rules['special_cases'] ?? [];
        foreach ($specialCases as $pattern => $rule) {
            if (stripos($majorName, $pattern) !== false) {
                return self::buildSubjectList($rule, $majorName);
            }
        }
        
        // Use default rule
        $defaultRule = $rules['default'] ?? [
            'pkk_priority' => 1,
            'optional_priority' => 2,
            'total_subjects' => 2
        ];
        
        return self::buildSubjectList($defaultRule, $majorName);
    }

    /**
     * Build subject list based on rule
     * For SMK: Always 1 PKK + 1 optional = 2 total
     */
    private static function buildSubjectList($rule, $majorName = '')
    {
        $subjects = [];
        
        // Add PKK subject (always first for SMK)
        $pkkSubject = self::getPKKSubject();
        $subjects[] = $pkkSubject;
        
        // Add additional subjects if specified
        if (isset($rule['additional_subjects'])) {
            $additionalSubjects = array_slice($rule['additional_subjects'], 0, $rule['total_subjects'] - 1);
            $subjects = array_merge($subjects, $additionalSubjects);
        } else {
            // Add default optional subject based on major name
            $optionalSubject = self::getRelevantOptionalSubject($majorName);
            $subjects[] = $optionalSubject;
        }
        
        // Ensure we have exactly 2 subjects (1 PKK + 1 optional)
        return array_slice($subjects, 0, 2);
    }
    
    /**
     * Get relevant optional subject based on major name
     */
    private static function getRelevantOptionalSubject($majorName)
    {
        $optionalSubjects = self::getOptionalSubjects();
        
        // Simple mapping based on major name
        $mapping = [
            'Matematika' => 'Matematika Lanjutan',
            'Fisika' => 'Fisika',
            'Kimia' => 'Kimia',
            'Biologi' => 'Biologi',
            'Ekonomi' => 'Ekonomi',
            'Sosiologi' => 'Sosiologi',
            'Sejarah' => 'Sejarah',
            'Geografi' => 'Geografi',
            'Bahasa' => 'Bahasa Indonesia Lanjutan',
            'Linguistik' => 'Bahasa Indonesia Lanjutan',
            'Sastra' => 'Bahasa Indonesia Lanjutan',
            'Seni' => 'Seni Budaya',
            'Filsafat' => 'Sosiologi',
        ];
        
        foreach ($mapping as $keyword => $subject) {
            if (stripos($majorName, $keyword) !== false) {
                return $subject;
            }
        }
        
        // Default fallback
        return $optionalSubjects[0] ?? 'Matematika Lanjutan';
    }

    /**
     * Check if major is SMK
     */
    public static function isSMK($rumpunIlmu)
    {
        $smkRumpun = ['HUMANIORA']; // Add more SMK rumpun if needed
        return in_array($rumpunIlmu, $smkRumpun);
    }

    /**
     * Get all SMK subjects configuration
     */
    public static function getAllConfig()
    {
        return self::loadConfig();
    }
}
