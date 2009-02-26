<?php
 /**
 * Copyright (c) STMicroelectronics, 2008. All Rights Reserved.
 *
 * Originally written by Ikram BOUOUD, 2008
 *
 * This file is a part of CodeX.
 *
 * CodeX is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * CodeX is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with CodeX; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * 
 */
class permsVisitor {
 
    var $group_id;
    var $allTreeItems = array();
    var $sep          = ',';
    var $node;
    
    public function permsVisitor($group_id) {
        require_once(dirname(__FILE__).'/../../docman/include/Docman_ItemFactory.class.php');
        require_once('common/user/UserManager.class.php');
        $um   = UserManager::instance();
        $user = $um->getCurrentUser();
        
        $params['user']            = $user;
        $params['ignore_collapse'] = true;
        $params['ignore_perms']    = true;
        $params['ignore_obsolete'] = false;
        
        $itemFactory    = new Docman_ItemFactory($group_id);
        $node           = $itemFactory->getItemTree(0, $params);
        $this->node     = $node;
        $this->group_id = $group_id;
    }
   
    
    /**
     * Method visitFolder which memorize Folder id and title and walk through its tree 
     * @param Tree  $item is the Folder tree
     * @param Array $docmanItem containing information about Docman items
     * @return null
     */
   
    public function visitFolder($item ,$docmanItem) {
        $id              = $item->getId();
        $title           = $item->getTitle();  
        $docmanItem[$id] = $title;
        $this->allTreeItems[] = $docmanItem;
        // Walk through the tree
        $items = $item->getAllItems();
        $it    = $items->iterator();
        while($it->valid()) {
            $i = $it->current();
            $i->accept($this, $docmanItem);
            $it->next();
        }
    }

    /**
     * Method visitWiki which memorize Wikipage id and title
     * @param Tree  $item is the Wikipage
     * @param Array $docmanItem is the array containing information about each Docman items
     * @return null
     */
   
    public function visitWiki($item ,$docmanItem) {
        $id                   = $item->getId();
        $title                = $item->getTitle(); 
        $docmanItem[$id]      = $title;
        $this->allTreeItems[] = $docmanItem;
    }

    /**
     * Method visitEmpty which memorize Empty file id and title
     * @param Tree $item is the Empty file
     * @param Array $docmanItem is the array containing information about each Docman items
     * @return null
     */

    public function visitEmpty($item ,$docmanItem) {
        $id                   = $item->getId();
        $title                = $item->getTitle(); 
        $docmanItem[$id]      = $title;
        $this->allTreeItems[] = $docmanItem;
    }
    
    /**
     * Method visitEmbeddedFile which memorize Embedded File id and title
     * @param Tree $item is the Embedded File
     * @param Array $docmanItem is the array containing information about each Docman items
     * @return null
     */
    
    public function visitEmbeddedFile($item ,$docmanItem) {
        $id                   = $item->getId();
        $title                = $item->getTitle(); 
        $docmanItem[$id]      = $title;
        $this->allTreeItems[] = $docmanItem;
    }
    
    /**
     * Method visitLink which  memorize Link id and title
     * @param Tree $item is the Link
     * @param Array $docmanItem is the array containing information about each Docman items
     * @return null
     */
    
    public function visitLink($item ,$docmanItem) {
        $id                   = $item->getId();
        $title                = $item->getTitle(); 
        $docmanItem[$id]      = $title;
        $this->allTreeItems[] = $docmanItem;
    } 
    
    /**
     * Method visitFile which memorize File  id and title
     * @param Tree $item is the Link
     * @param Array $docmanItem is the array containing information about each Docman items
     * @return null
     */
    
    public function visitFile($item ,$docmanItem) {
        $id                   = $item->getId();
        $title                = $item->getTitle(); 
        $docmanItem[$id]      = $title;
        $this->allTreeItems[] = $docmanItem;
    }
    
    /**
     *  Method itemFullName which append the full name  of the Doc/Folder and memorize its Id and its full name in listItem
     * @param  Array tableId contains item's id and short name
     * @param  Array visited_id contains item's which are already visited to donot visit them again
     * @return Array listItem will memorize item id and its full name
     */ 
    
    private function itemFullName($tableId,&$visited_id) { 
        $item_full_name = '';
        $item_id        = '';
        foreach($tableId as $id => $idDoc) {
            if (!array_search($id,$visited_id)) {
                $item_full_name .= $idDoc."/";
                $item_id         = $id;
                $listItem[$item_id] = $item_full_name;
                $visited_id[] = $id;
            }
        }
        return $listItem;
    }
    
    /**
     * Method cvsFormatting which extract information from arraies and print them in the csv format
     * @param Array ugroups will memorize all ugroup's name and id
     * @param Array listItem contains items ids and names
     * @param int   group_id project id
     * @return null
     */
    
    public function csvFormatting() {
       
        echo "Document/Folder,User group,Read,Write,Manage\n";
        
        $table_perms = array();
        $docmanItem  = array();
        $listItem    = array();
        $visited_ids = array();
        
        $ugroups     = $this->listUgroups();
        $this->visitFolder($this->node, $docmanItem);
        
        foreach ($this->allTreeItems as $folder_id ) {
            $listItem[] = $this->itemFullName($folder_id,$visited_ids);  
        }

        foreach($listItem as $item_ids => $items) {
            foreach ($items as $item_id => $item) {
                $permission_type = 'PLUGIN_DOCMAN%';
                $table_perms     = $this->extractPermissions($item_id, $permission_type);
                foreach ($table_perms as $row_permissions) {
                    $permission = $this->permissionFormatting($row_permissions['permission_type']);
                    echo $item.$this->sep.$row_permissions['name'].$this->sep.$permission.PHP_EOL;
                }
            }
        }  
    }
    
    public function showDefinitionFormat() {
        project_admin_header(array('title'=>$GLOBALS['Language']->getText('plugin_eac','export_format')));
        
        echo '<h3>'.$GLOBALS['Language']->getText('plugin_eac','perm_exp_format').'</h3>';
        echo '<p>'.$GLOBALS['Language']->getText('plugin_eac','perm_exp_format_msg').'</p>';
        $title_arr = array(
            $GLOBALS['Language']->getText('plugin_eac','format_label'),
            $GLOBALS['Language']->getText('plugin_eac','format_sample'),
            $GLOBALS['Language']->getText('plugin_eac','format_description')
        );
        echo  html_build_list_table_top ($title_arr);
        echo "<tr class='". util_get_alt_row_color(0) ."'>";
        echo "<td><b>".$GLOBALS['Language']->getText('plugin_eac','format_path')."</b></td>";
        echo "<td>Project Documentation/My Document</td>";
        echo "<td>".$GLOBALS['Language']->getText('plugin_eac','format_path_desc')."</td>";
        echo "</tr>";
        echo "<tr class='". util_get_alt_row_color(1) ."'>";
        echo "<td><b>".$GLOBALS['Language']->getText('plugin_eac','format_user_group')."</b></td>";
        echo "<td>Developper Group</td>";
        echo "<td>".$GLOBALS['Language']->getText('plugin_eac','format_user_group_desc')."</td>";
        echo "</tr>";
        echo "<tr class='". util_get_alt_row_color(2) ."'>";
        echo "<td><b>".$GLOBALS['Language']->getText('plugin_eac','format_read')."</b></td>";
        echo "<td>yes</td>";
        echo "<td>".$GLOBALS['Language']->getText('plugin_eac','format_read_desc')."</td>";
        echo "</tr>";
        echo "<tr class='". util_get_alt_row_color(3) ."'>";
        echo "<td><b>".$GLOBALS['Language']->getText('plugin_eac','format_write')."</b></td>";
        echo "<td>no</td>";
        echo "<td>".$GLOBALS['Language']->getText('plugin_eac','format_write_desc')."</td>";
        echo "</tr>";
        echo "<tr class='". util_get_alt_row_color(4) ."'>";
        echo "<td><b>".$GLOBALS['Language']->getText('plugin_eac','format_manage')."</b></td>";
        echo "<td>no</td>";
        echo "<td>".$GLOBALS['Language']->getText('plugin_eac','format_manage_desc')."</td>";
        echo "</tr>";        
        echo "</table>";
        site_project_footer( array() );
    }
    
    /**
     * Method permissionFormatting which print information about permission in csv format
     * @param Tree item is the Embedded File
     * @return String 
     */
    
    private function permissionFormatting($permissionType) {
        if($permissionType == 'PLUGIN_DOCMAN_MANAGE') {
            return 'yes'.$this->sep.'yes'.$this->sep.'yes';
        } else if ($permissionType == 'PLUGIN_DOCMAN_READ') {
            return 'yes'.$this->sep.'no'.$this->sep.'no';
        } else if($permissionType == 'PLUGIN_DOCMAN_WRITE') {
            return 'yes'.$this->sep.'yes'.$this->sep.'no';
        }
    }

    /**
     * Method listUgroup which extract user groups list for a given project
     * @param int   group_id project id
     * @param Array ugroups will memorize all ugroup's name and id
     * @return null
     */
    
    private function listUgroups() {
        $sql = sprintf('SELECT Ugrp.ugroup_id, Ugrp.name'.
                       ' FROM  ugroup Ugrp '.
                       ' WHERE Ugrp.group_id = %d',
                       db_ei($this->group_id));

        $result_list_ugroups = db_query($sql);
        while($row_list_ugroups = db_fetch_array($result_list_ugroups)) {
            $ugroup_id = $row_list_ugroups['ugroup_id'];
            $ugroups[] = $row_list_ugroups;
        }
        return $ugroups;
    }
    
    /**
     * Method extractPermissions which extract permissions of a  user group on an item
     * @param int group_id project id
     * @param int ugroup_id user group id
     * @param int item_id item id
     * @return array row_perms permission information
     */
    
    private function extractPermissions($item_id, $permission_type){
        $table_perms = array();
        
        $sql = sprintf('SELECT DISTINCT Ugrp.ugroup_id, Ugrp.name, P.permission_type, PDI.title'.
                       ' FROM plugin_docman_item PDI,permissions P,ugroup Ugrp '.
                       ' WHERE (Ugrp.group_id = %d) ' .
                       ' AND PDI.item_id = %d ' .
                       ' AND P.ugroup_id = Ugrp.ugroup_id ' .
                       ' AND P.permission_type LIKE \'%s\' ' .
                       ' AND PDI.item_id = P.object_id ' .
                       ' AND PDI.group_id = Ugrp.group_id',
                        db_ei($this->group_id), db_ei($item_id), db_es($permission_type));
        $result_perms = db_query($sql);
        
        while ($row_perms = db_fetch_array($result_perms)) {
                $table_perms[] = $row_perms;
        }
        return $table_perms;
     }
}
?>