<?php
/**
 * Clase que define los links a funcionalidades propias del bloque actividad_social
 * @author 2015 Hans Jeria (hansjeria@gmail.com)
 *
 */

class block_reporte extends block_base {

	// Inicializa el bloque
	function init() {
		$this->title = "Resumen de actividades";
		$this->version = 2015060300;
	}

	// Función que genera el contenido del bloque
	function get_content() {
		global $OUTPUT, $USER, $CFG, $DB, $PAGE, $COURSE;
		
		if ($this->content !== NULL) {
			return $this->content;
		}

			$this->content = new stdClass;
			$course = $PAGE->course;
			
			//Mis tareas hechas
			$params = array(1,1,$course->id,$USER->id);
			$sql_myassing = "SELECT asub.id, a.name, us.firstname, us.lastname, asub.timecreated, asub.timemodified
						 	FROM {course_modules} as cm INNER JOIN {modules} as m ON (cm.module = m.id) 
						   		INNER JOIN {assign} as a ON (a.course = cm.course) 
    					   		INNER JOIN {assign_submission} as asub ON ( asub.assignment = a.id) 
    							INNER JOIN {user} as us ON (us.id = asub.userid) 
						 	WHERE m.name in ('assign') 
								AND cm.visible = ? 
    							AND m.visible = ?
    							AND cm.course = ?
								AND us.id = ?
							ORDER BY asub.timemodified DESC,asub.id";
			$myassings = $DB->get_records_sql($sql_myassing, $params);
			
			// Total tareas
			$params = array(1,1,$course->id);
			$sql_allassing = "SELECT asub.id
						 	FROM mdl_course_modules as cm INNER JOIN mdl_modules as m ON (cm.module = m.id)
						   		INNER JOIN mdl_assign as a ON (a.course = cm.course)
    					   		INNER JOIN mdl_assign_submission as asub ON ( asub.assignment = a.id)
						 	WHERE m.name in ('assign')
								AND cm.visible = '1'
    							AND m.visible = '1'
    							AND cm.course = '11'
							GROUP BY asub.id";
			$allassings = $DB->get_records_sql($sql_allassing, $params);
			
			
			
			//Traer los quiz
			$sql_quiz = "SELECT qatt.id, q.name, us.firstname, us.lastname, qatt.timestart, qatt.timefinish
						 	FROM {course_modules} as cm INNER JOIN {modules} as m ON (cm.module = m.id) 
						   		INNER JOIN {quiz} as q ON (q.course = cm.course) 
    					   		INNER JOIN {quiz_attempts} as qatt ON ( qatt.quiz = q.id) 
    							INNER JOIN {user} as us ON (us.id = qatt.userid) 
						 	WHERE m.name in ('quiz')
								AND cm.visible = ? 
    							AND m.visible = ?
    							AND cm.course = ?
    					  	ORDER BY qatt.timefinish DESC, qatt.id";
			$lastquiz = $DB->get_records_sql($sql_quiz, $params);
			
			// Traer los recursos
			$params = array(1,1,$course->id,$USER->id);
			$sql_resources = "SELECT log.id, r.name, us.firstname, us.lastname, log.timecreated
						 	FROM {course_modules} as cm INNER JOIN {modules} as m ON (cm.module = m.id) 
						   		INNER JOIN {resource} as r ON (r.course = cm.course)
								INNER JOIN {logstore_standard_log} as log ON (log.objectid = r.id)
								INNER JOIN {user} as us ON (us.id = log.userid)
						 	WHERE m.name in ('resource')
								AND log.objecttable = 'resource'
								AND cm.visible = ? 
    							AND m.visible = ?
    							AND cm.course = ?
								AND us.id = ?
    					  	ORDER BY log.timecreated DESC, log.id ";
			$myresources = $DB->get_records_sql($sql_resources, $params);
			
			// Creación de tabla que muestra las últimas 5 tareas enviadas
			$table_reports = new html_table();
			$table_reports->head = array('', '');
			$table_reports->data[] = array("Total tareas enviadas",count($myassings));
			$table_reports->data[] = array("Total no tareas enviadas",(count($allassings)-count($myassings)));
			$table_reports->data[] = array("Recursos visualizados",count($myresources));
			

					
			$seemore = new moodle_url('../local/reportesalumnos/index.php', array('cmid'=>$course->id));
			
			
			$this->content->text = html_writer::table($table_reports).$OUTPUT->single_button($seemore,"Ver más");
			$this->content->footer = "";
			
			return $this->content;
		
		

	}


	

}
