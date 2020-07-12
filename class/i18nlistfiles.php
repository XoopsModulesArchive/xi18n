<?php
/**
 * class for List files and directories
 * 
 * 
 * @author LudoO <LudoO@mail.com> <http://ludoo.nuxit.net>
 * 
 */
class I18nListFiles
{
		/**
		 * List recursively
		 * @var boolean
		 */
		var $recurse = false;

		/**
		 * List directories
		 * @var boolean
		 */
		var $list_dirs = false;
		
		/**
		 * List files
		 * @var boolean
		 */
		var $list_files = false;
		
		/**
		 * Return relative path
		 * @var boolean
		 */
		var $relative_path = true;
		
		/**
		 * assign a boolean to the recurse property
		 * 
         * @param boolean $recurse
		 */
		function setRecurse($recurse)
		{
			$this->recurse = $recurse;
		}
		
		/**
		 * assign a boolean to the list_dirs property
		 * 
         * @param boolean $list_dirs
		 */
		function setListDirs($list_dirs)
		{
			$this->list_dirs = $list_dirs;
		}
		
		/**
		 * assign a boolean to the list_files property
		 * 
         * @param boolean $list_files
		 */
		function setListFiles($list_files)
		{
			$this->list_files = $list_files;
		}
		
		/**
		 * assign a boolean to the relative_path property
		 * 
         * @param boolean $relative_path
		 */
		function setRelativePath($relative_path)
		{
			$this->relative_path = $relative_path;
		}
		
        function ListDirs($dir, &$files) {
		   $this->setListDirs(1); 
		   $this->setListFiles(0);
		   $this->setRecurse(0);
		   $this->getList($dir, $files, '');
        }
        
        function ListFiles($dir, &$files, $recurse=1) {
		   $this->setListDirs(0); 
		   $this->setListFiles(1);
		   $this->setRecurse($recurse);
           $this->getList($dir, $files, '');
        }
        
        function getList($dir, &$files, $basedir='') {
            if (is_dir($dir)) {
              if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                  if($file!='.'&&$file!='..'){
                     if (is_dir($dir.'/'.$file)){
                        if ($this->list_dirs){
                           if ($this->relative_path)
						      $files[] = $basedir.$file;
						   else
						      $files[] = $dir.'/'.$file;
                        }
                        //Recurse into folder
                        if ($this->recurse)
                           $this->getList($dir.'/'.$file, $files, $basedir.$file.'/');
                     }else{
                        if ($this->list_files){
                           if ($this->relative_path)
						      $files[] = $basedir.$file;
						   else
						      $files[] = $dir.'/'.$file;
                        }
                     }
                  }
                }
                closedir($dh);
              }
            }
        }
}
?>