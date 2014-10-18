<script type="text/javascript">
    $(function() {
        $(document).off("savedIngredient.editRecipe");
        $(document).on("savedIngredient.editRecipe", function() {
            $('#editIngredientDialog').dialog('close');
        });
        
        $("#sortableTable1 tbody.content").sortable({
            stop: function( event, ui ) { 
                reNumberTable("sortableTable1");
                console.log("I changed, time to ")}
        });
        $("#sortableTable2 tbody.content").sortable();
        
        $('#ingredientsSection .fraction input').each(function() {
            $(this).change(function() {
                fractionConvert($(this));
            });
        })
    });
    
    function reNumberTable(tableId) {
        
    }
    
    function fractionConvert($item) {
        var teststring = $item.val();
        var a=teststring.indexOf(",");      // change "," to "." (in all languages)
        if ( a !== -1 ) {                   //FIXME: bug - still displays "." for all languages
            $item.val(teststring=teststring.substring(0,a)+"."+teststring.substring(a+1,teststring.length));
        }
        
        if (isNaN(teststring))
        {
            if (teststring.indexOf("/")>0) {
                if (teststring.indexOf(" ")>0) {
                        n = teststring.substring(0,teststring.indexOf(" ")+1);
                        f = teststring.substring(teststring.indexOf(" ")+1);
                } else {
                        n = teststring.substring(0,teststring.indexOf("/")-1);
                        f = teststring.substring(teststring.indexOf("/")-1);
                }
                if (isNaN(n)){alert(numberErrorHtml);return;}//Make shure we have a number
                var newArray = f.split("/");
                if (isNaN(newArray[0])){alert(numberErrorHtml);return;}//Make shure we have a number
                if (isNaN(newArray[1])){alert(numberErrorHtml);return;}
                $item.val(eval((n*1)+(newArray[0]/newArray[1])));//write the new value to the calling box
            } else {
                alert(numberErrorHtml)
            }
        }
    }
</script>
<?php //echo $this->element('sql_dump'); ?>
<pre><?php //print_r($recipe); ?></pre>

<div class="actions">
	<ul>
            <li><?php echo $this->Html->link(__('Edit Sources'), array('controller' => 'sources', 'action' => 'index'), array('class' => 'ajaxLink', 'targetId' => 'content')); ?>
            <li><?php echo $this->Html->link(__('Import'), array('controller' => 'import'), array('class' => 'ajaxLink', 'targetId' => 'content')); ?></li>
            <li><?php echo $this->Html->link(__('Export'), array('controller' => 'export'), array('class' => 'ajaxLink', 'targetId' => 'content')); ?></li>
            <li><button id="moreActionLinks">More Actions...</button></li>
	</ul>
        <div style="display: none;">
            <ul id="moreActionLinksContent">
                <li><?php echo $this->Html->link(__('Edit Ethnicities'), array('controller' => 'ethnicities', 'action' => 'index'), array('class' => 'ajaxLink', 'targetId' => 'content')); ?> </li>
                <li><?php echo $this->Html->link(__('Edit Base Types'), array('controller' => 'base_types', 'action' => 'index'), array('class' => 'ajaxLink', 'targetId' => 'content')); ?> </li>
                <li><?php echo $this->Html->link(__('Edit Courses'), array('controller' => 'courses', 'action' => 'index'), array('class' => 'ajaxLink', 'targetId' => 'content')); ?> </li>
                <li><?php echo $this->Html->link(__('Edit Preparation Times'), array('controller' => 'preparation_times', 'action' => 'index'), array('class' => 'ajaxLink', 'targetId' => 'content')); ?> </li>
                <li><?php echo $this->Html->link(__('Edit Difficulties'), array('controller' => 'difficulties', 'action' => 'index'), array('class' => 'ajaxLink', 'targetId' => 'content')); ?> </li>
                <li> </li>
            </ul>
        </div>     
</div>
<div class="recipes form">
<?php echo $this->Form->create('Recipe', array('default' => false, 'type' => 'file')); ?>
    <fieldset>
            <legend><?php echo __('Recipe'); ?></legend>
            <?php
            echo $this->Form->hidden('id');
            echo $this->Form->input('name');
            echo $this->Form->input('comments', array('escape' => true, 'rows' => '2'));
            echo $this->Form->input('source_id', 
                    array('empty'=>true, 
                        'after' => $this->Html->link(__('Edit'), 
                            array('controller' => 'sources', 'action' => 'index'), 
                            array('class' => 'nagivationLink', 'targetId' => 'content', 'id' => 'sourcesEditLink'))));
            echo $this->Form->input('source_description');
            echo $this->Form->input('course_id', array('empty'=>true));
            echo $this->Form->input('base_type_id', array('empty'=>true));
            echo $this->Form->input('preparation_method_id', array('empty'=>true));
            echo $this->Form->input('ethnicity_id', array('empty'=>true));
            echo $this->Form->input('preparation_time_id', array('empty'=>true));
            echo $this->Form->input('difficulty_id', array('empty'=>true));
            echo $this->Form->input('serving_size');
            echo $this->Form->input('picture', array('type'=>'file'));
            echo $this->Form->hidden('picture_type');
            echo $this->Form->input('private', array('options' => array('0' => 'No', '1' => 'Yes')));
            echo $this->Form->input('system', array('options' => array('usa' => 'USA', 'metric' => 'Metric')));
            echo $this->Form->input('user_id');
            ?>
            <div id="ingredientsSection">
                <table id="sortableTable1">
                <tr>
                    <th class="deleteIcon"></th><th class="moveIcon"></th>
                    <th><?php echo __('Quantity');?></th>
                    <th><?php echo __('Units');?></th>
                    <th><?php echo __('Qualifier');?></th>
                    <th><?php echo __('Ingredient') . " - ";?>
                        <?php echo $this->Html->link(__('add new'), array('controller'=>'ingredients', 'action' => 'edit'), 
                                array('class' => 'ajaxLink', 'targetId' => 'editIngredientDialog', 'id'=>'addNewIngredientsLink'));?>
                    </th>
                    <th><?php echo __('Optional');?></th>
                </tr>
                <tbody class="content">
                <?php 
                for ($mapIndex = 0; $mapIndex <= count($recipe['IngredientMapping']); $mapIndex++) {
                    $currentSortOrder = __("Unknown");
                    
                    if ($mapIndex < count($recipe['IngredientMapping']))
                        $currentSortOrder = $recipe['IngredientMapping'][$mapIndex]['sort_order'];
                    
                        
                ?>
                <tr>
                    <td>
                        <div class="ui-state-default ui-corner-all deleteIcon" title="<?php echo __('Delete'); ?>">
                            <span class="ui-icon ui-icon-trash"></span>
                        </div>
                    </td>
                    <td>
                        <div class="ui-state-default ui-corner-all moveIcon" title="<?php echo __('Order Ingredient - currently #' . $currentSortOrder );?>">
                            <span class="ui-icon ui-icon-arrow-4"></span>
                        </div>
                    </td>
                    <td>
                        <?php echo $this->Form->hidden('IngredientMapping.' . $mapIndex . '.id'); ?>
                        <?php echo $this->Form->hidden('IngredientMapping.' . $mapIndex . '.recipe_id'); ?>
                        <?php echo $this->Form->hidden('IngredientMapping.' . $mapIndex . '.ingredient_id'); ?>
                        <?php echo $this->Form->hidden('IngredientMapping.' . $mapIndex . '.sort_order'); ?>
                        
                        <?php echo $this->Form->input('IngredientMapping.' . $mapIndex . '.quantity', array('label' => false, 'type' => 'fraction')); ?></td>
                    <td><?php echo $this->Form->input('IngredientMapping.' . $mapIndex . '.unit_id', array('label' => false)); ?></td>
                    <td><?php echo $this->Form->input('IngredientMapping.' . $mapIndex . '.qualifier', array('label' => false, 'escape' => false)); ?></td>
                    <td><?php echo $this->Form->input('IngredientMapping.' . $mapIndex . '.Ingredient.name', array('label' => false, 'escape' => false)); ?></td>
                    <td><?php echo $this->Form->input('IngredientMapping.' . $mapIndex . '.optional', array('label' => false)); ?></td> 
                </tr>
                <?php } ?>
                </tbody>
                </table>
            </div>

            <?php 
            echo $this->Form->input('directions', array('escape' => true, 'rows' => '20', 'cols' => '20'));
            ?>
            
            <div id="relatedRecipes">
                <table id="sortableTable2">
                <tr>
                    <th class="deleteIcon"></th><th class="moveIcon"></th>
                    <th><?php echo __('Related Recipe Name');?></th>
                    <th><?php echo __('Required');?></th>
                </tr>
                <tbody class="content">
                <?php 
                for ($mapIndex = 0; $mapIndex <= count($recipe['RelatedRecipe']); $mapIndex++) {
                    $currentSortOrder = __("Unknown");
                    
                    if ($mapIndex < count($recipe['RelatedRecipe']))
                        $currentSortOrder = $recipe['RelatedRecipe'][$mapIndex]['sort_order'];
                       
                ?>
                <tr>
                    <td>
                        <div class="ui-state-default ui-corner-all deleteIcon" title="<?php echo __('Delete'); ?>">
                            <span class="ui-icon ui-icon-trash"></span>
                        </div>
                    </td>
                    <td>
                        <div class="ui-state-default ui-corner-all moveIcon" title="<?php echo __('Order Recipe - currently #' . $currentSortOrder);?>">
                            <span class="ui-icon ui-icon-arrow-4"></span>
                        </div>
                    </td>
                    <td>
                        <?php echo $this->Form->hidden('RelatedRecipe.' . $mapIndex . '.id'); ?>
                        <?php echo $this->Form->hidden('RelatedRecipe.' . $mapIndex . '.parent_id'); ?>
                        <?php echo $this->Form->hidden('RelatedRecipe.' . $mapIndex . '.recipe_id'); ?>
                        <?php echo $this->Form->hidden('RelatedRecipe.' . $mapIndex . '.sort_order'); ?>
                        
                        <?php echo $this->Form->input('RelatedRecipe.' . $mapIndex . '.Related.name', array('label' => false, 'escape' => false)); ?></td>
                    <td><?php echo $this->Form->input('RelatedRecipe.' . $mapIndex . '.required', array('label' => false)); ?></td> 
                </tr>
                <?php } ?>
                </tbody>
                </table>
            </div>
    </fieldset>
<?php echo $this->Session->flash(); ?> 
<?php echo $this->Form->end(__('Submit')); ?>
</div>


