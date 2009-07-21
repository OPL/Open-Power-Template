<?php

function _debugPrintNodes($node)
		{
			echo '<ul>';

			foreach($node as $id => $subnode)
			{
				if(!is_object($subnode))
				{
					echo '<li><font color="red"><strong>Non-object value detected in the node list! Type: '.gettype($subnode).'</strong></font></li>';
					continue;
				}

				$hidden = $subnode->get('hidden') ? ' (HIDDEN)' : '';
				switch($subnode->getType())
				{
					case 'Opt_Xml_Cdata':
						echo '<li>'.$id.': <strong>Character data:</strong> '.htmlspecialchars($subnode).$hidden.'</li>';
						break;
					case 'Opt_Xml_Comment':
						echo '<li>'.$id.': <strong>Comment:</strong> '.htmlspecialchars($subnode).$hidden.'</li>';
						break;
					case 'Opt_Xml_Text':
						echo '<li>'.$id.': <strong>Text:</strong> ';
						_debugPrintNodes($subnode);
						echo $hidden.'</li>';
						break;
					case 'Opt_Xml_Expression':
						echo '<li>'.$id.': <strong>Expression:</strong> '.$subnode.$hidden.'</li>';
						break;
					case 'Opt_Xml_Element':
						echo '<li>'.$id.': <strong>Element node:</strong> '.$subnode->getXmlName().' (';
						$args = $subnode->getAttributes();
						foreach($args as $name => $value)
						{
							echo $name.'="'.$value.'" ';
						}
						echo ')';
						if($subnode->get('single') === true)
						{
							echo ' single';
						}
						_debugPrintNodes($subnode);
						echo $hidden.'</li>';
						break;
				}
			}
			echo '</ul>';
		} // end _debugPrintNodes();

$xml = <<<XMLV
<?xml version="1.0" ?>
<opt:root xmlns:opt="http://xml.invenzzia.org/namespaces/opt">
	<p>Hi universe!</p>
	<opt:section name="foo">
		<p>Lol</p>
	</opt:section>
	<!-- komentarz -->
	A teraz beda jaja
	<p>Foobar</p>
	<foo>
		<bar opt:if="\$foo">A</bar>
		<bar>B</bar>
		<bar>C</bar>
	</foo>
</opt:root>
XMLV;
	require('./init.php');

    try
    {
		session_start();
    	$tpl = new Opt_Class;
    	$tpl->sourceDir = './templates/';
    	$tpl->compileDir = './templates_c/';
    	$tpl->charset = 'utf-8';
    	$tpl->compileMode = Opt_Class::CM_REBUILD;
    	$tpl->stripWhitespaces = false;
    	$tpl->setup();

    	$tree = new Opt_Parser_Xml;
		_debugPrintNodes($tree->parse('sample.tpl',$xml));

    }
    catch(Opt_Exception $exception)
    {
    	Opt_Error_Handler($exception);
    }
    catch(Opl_Exception $exception)
    {
    	Opl_Error_Handler($exception);
    }