<div class="settings global_form_popup">
    <?php echo $this->form->setAttrib('class', 'global_form')->render($this) ?>
</div>

<?php if (@$this->closeSmoothbox): ?>
    <script type="text/javascript">
        TB_close();
    </script>
<?php endif; ?>