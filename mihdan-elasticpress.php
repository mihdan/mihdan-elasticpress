<?php
/**
 * Plugin Name:  Mihdan: ElasticPress
 * Plugin URI:   https://github.com/mihdan/mihdan-elasticpress
 * Description:  Basic WordPress Plugin Header Comment
 * Version:      0.4
 * Author:       Mikhail Kobzarev
 * Author URI:   https://www.kobzarev.com/
 * License:      GPL2
 * License URI:  https://www.gnu.org/licenses/gpl-2.0.html
 */

namespace Mihdan_Elastic_Press;

/**
 * Init Elasticsearch PHP Client
 */
use Elasticsearch\ClientBuilder;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'ep_get_host' ) ) {
	return;
}

class Core {

	private static $instance;

	public static function get_instance() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function setup() {}
	public function hooks() {
		add_action( 'rest_api_init', array( $this, 'register_rest_route' ) );
		add_filter( 'ep_related_posts_fields', array( $this, 'set_related_posts_fields', 10, 1 ) );
		add_filter( 'ep_config_mapping', array( $this, 'config_mapping', 10, 1 ) );
	}

	private function __construct() {
		$this->setup();
		$this->hooks();
	}

	/**
	 * Register Elasticpress Autosuggest Endpoint
	 *
	 * This is the endpoint you have to specify in the admin
	 * like this: http(s)://domain.com/wp-json/elasticpress/v1.0/autosuggest/
	 */
	public function register_rest_route() {

		$args = [
			'methods'  => \WP_REST_Server::CREATABLE,
			'callback' => array( $this, 'autosuggest' ),
		];

		register_rest_route( 'elasticpress/v1.0', '/autosuggest/', $args );
	}

	/**
	 * Elasticpress Autosuggest Endpoint Callback
	 *
	 * gets host and index name dynamically. Otherwise,
	 * if not specified, host would default to localhost:9200
	 * and index name would default to 'index'
	 *
	 * @param \WP_REST_Request $data
	 * @return array|callable
	 */
	public function autosuggest( \WP_REST_Request $data ) {
//		$client = ClientBuilder::create();
//		$client->setHosts( [ \ep_get_host() ] ); // get host dynamically
//		$client   = $client->build();
//		$params   = [
//			'index' => ep_get_index_name(), // get index dynamically
//			'type'  => 'post',
//			'body'  => $data->get_body(),
//		];
//		$response = $client->search( $params );
//
//		return $response;
	}

	public function set_related_posts_fields( $fields ) {
		$fields = array( 'post_title' );//, 'post_content', 'terms.post_tag.name' );
		return $fields;
	}

	function config_mapping( $mapping ) {


		$mapping['settings']['analysis']['filter']['russian_stop'] = array(
			'type'      => 'stop',
			//'stopwords' => '_russian_',
			'stopwords' => 'а,без,более,больше,большой,будет,бы,был,была,были,было,быть,в,вам,вас,вдоль,ведь,весь,видно,вместо,вне,вниз,внизу,внутри,во,вокруг,восемь,вот,все,всегда,всего,всей,всех,вся,всё,вы,где,говорил,говорили,говорим,говорить,говорят,год,да,давай,давать,давно,даже,далеко,два,девять,день,десять,для,до,долго,достаточно,другие,другого,другое,другой,его,ее,ей,если,есть,еще,ещё,же,за,зачем,здесь,знать,и,ибо,из,изо,или,именно,иметь,иной,ином,их,к,каждого,каждому,каждый,каждым,как,какое,какой,когда,который,кроме,кто,ли,либо,лишь,между,меня,мне,много,мог,могу,может,мои,мой,мы,на,навсегда,над,надо,назад,нам,нас,наш,не,него,недавно,нее,ней,некто,нельзя,несколько,нет,неё,ни,нибудь,никто,них,но,ноль,ну,о,оба,обо,один,однако,около,он,она,они,оно,оный,опять,особенно,от,ото,отчего,очень,по,под,пожалуйста,после,потому,похоже,почему,почти,при,про,прямо,пусть,пять,равно,раз,ребята,редко,с,сам,самая,сами,самим,самой,самому,самый,самым,свой,себя,семь,сказал,сказали,сказать,сначала,снова,со,совсем,спасибо,сразу,среди,ста,стал,стала,стали,стать,сто,так,также,такие,такой,там,твой,тем,теперь,то,тогда,того,тоже,той,только,том,тот,точно,три,тут,ты,тысяч,тысяча,тысячи,тысячу,у,уж,уже,хоть,хотя,час,часто,часу,чего,чей,чем,четыре,что,чтоб,чтобы,чье,чья,чьё,шесть,эта,эти,это,этого,этом,этот,я',
		);

		//$mapping['settings']['analysis']['filter']['russian_keywords'] = array(
		//	'type'     => 'keyword_marker',
		//	'keywords' => array(),
		//);

		$mapping['settings']['analysis']['filter']['russian_stemmer'] = array(
			'type'     => 'stemmer',
			'language' => 'russian',
		);

		//$mapping['settings']['analysis']['filter']['russian_stemmer'] = array(
		//	'type'     => 'snowball',
		//	'language' => 'russian',
		//);

		$mapping['settings']['analysis']['analyzer']['russian']['tokenizer'] = 'standard';

		$mapping['settings']['analysis']['analyzer']['russian']['filter'] = array(
			'lowercase',
			'russian_stop',
			//'russian_keywords',
			'russian_stemmer',
		);

		$mapping['mappings']['post']['properties']['post_title']['analyzer']   = 'russian';
		$mapping['mappings']['post']['properties']['post_content']['analyzer'] = 'russian';


		return $mapping;
	}
}

Core::get_instance();

// ep_analyzer_language

// eof;
