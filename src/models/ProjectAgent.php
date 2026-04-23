<?php
/**
 * ProjectAgent Model - Persona-driven AI suggestions
 */

class ProjectAgent {
    private $conn;
    private $type;
    private $persona;

    public function __construct($db, $type = 'researcher') {
        $this->conn = $db;
        $this->type = $type;
        $this->loadPersona();
    }

    private function loadPersona() {
        $personas = [
            'researcher' => [
                'name' => 'Dr. Insight',
                'goals' => 'Find mentors, validate ideas, publish findings',
                'tone' => 'Academic and thorough',
                'suggestions' => [
                    'Have you checked the latest IEEE papers on this topic?',
                    'Consider adding a Literature Review phase to your charter.',
                    'A Faculty Advisor with AI expertise would strengthen this idea.'
                ]
            ],
            'advisor' => [
                'name' => 'Mentor Marcus',
                'goals' => 'Guide student research, validate ideas',
                'tone' => 'Supportive and critical',
                'suggestions' => [
                    'The scope looks a bit too large for one semester.',
                    'How will you measure the success of this project?',
                    'Make sure to document your technical decisions in the log.'
                ]
            ],
            'lead' => [
                'name' => 'Commander Code',
                'goals' => 'Execute collaborative projects on time',
                'tone' => 'Direct and efficiency-focused',
                'suggestions' => [
                    'We need to assign owners to the remaining wave-1 tasks.',
                    'A blocker has been detected in the technical stack.',
                    'Let\'s schedule a quality gate review for the current phase.'
                ]
            ]
        ];
        $this->persona = $personas[$this->type] ?? $personas['researcher'];
    }

    public function getPersona() {
        return $this->persona;
    }

    public function getSuggestions($idea_id) {
        // In a real implementation, this would call an LLM with idea context
        // For the demo, we return persona-specific suggestions
        return $this->persona['suggestions'];
    }

    public function analyzeIdeaHealth($idea_id) {
        // Logic to check GSD progress and anti-patterns
        return [
            'health_score' => 85,
            'status' => 'Healthy',
            'recommendations' => [
                'Complete the Project Brief',
                'Assign a reviewer for Phase 1'
            ]
        ];
    }
}
?>
