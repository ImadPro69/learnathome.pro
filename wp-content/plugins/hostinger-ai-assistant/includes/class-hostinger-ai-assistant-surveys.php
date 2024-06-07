<?php

use Hostinger\Surveys\SurveyManager;
use Hostinger\WpHelper\Utils as Helper;

class Surveys {
	public const AI_SURVEY_ID = 'ai_plugin_survey';
	public const AI_SURVEY_LOCATION = 'wordpress_ai_plugin';
	public const AI_SURVEY_PRIORITY = 70;
	public const SUBMITTED_SURVEY_TRANSIENT = 'submitted_survey_transient';
	private SurveyManager $surveyManager;

	public function __construct( SurveyManager $surveyManager ) {
		$this->surveyManager = $surveyManager;
	}

	public function init() {
		add_filter( 'hostinger_add_surveys', [ $this, 'createSurveys' ] );
	}

	public function createSurveys( $surveys ) {
		if ( $this->isContentGenerationSurveyEnabled() ) {
			$scoreQuestion   = esc_html__( 'How would you rate your experience using our AI Assistant plugin for content generation? (Scale 1-10)', 'hostinger-ai-assistant' );
			$commentQuestion = esc_html__( 'Do you have any comments/suggestions to improve our AI tools?', 'hostinger-ai-assistant' );
			$aiSurvey       = SurveyManager::addSurvey( self::AI_SURVEY_ID, $scoreQuestion, $commentQuestion, self::AI_SURVEY_LOCATION, self::AI_SURVEY_PRIORITY );
			$surveys[]       = $aiSurvey;
		}

		return $surveys;
	}

	public function isContentGenerationSurveyEnabled(): bool {

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return false;
		}

		$helper               = new Helper();
		$notSubmitted         = ! get_transient( self::SUBMITTED_SURVEY_TRANSIENT );
		$notCompleted         = $this->surveyManager->isSurveyNotCompleted( self::AI_SURVEY_ID );
		$contentPublished     = get_option( 'hostinger_content_published', '' );
		$isClientEligible     = $this->surveyManager->isClientEligible();
		$isHostingerAdminPage = $helper->isThisPage( 'hostinger' );

		if ( ! $isHostingerAdminPage ) {
			return false;
		}

		return $notSubmitted && $notCompleted && $contentPublished && $isClientEligible;
	}

}
