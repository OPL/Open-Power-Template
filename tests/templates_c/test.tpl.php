<?php echo htmlspecialchars($this->_tf->_('foo','bar'));   ?>
<?php echo htmlspecialchars($this->_tf->_('foo','joe'));   ?>
<?php echo htmlspecialchars($this->_tf->assign('goo','bar',$this->_data['variable']));   ?>
<?php echo htmlspecialchars($this->_tf->_('goo','bar'));   ?>

