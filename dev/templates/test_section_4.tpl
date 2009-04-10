<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>
<opt:root>
<opt:section name="sect1">
<opt:section name="sect2" parent="foo">
<opt:section name="sect3">
{$sect1.value} {$sect2.value} {$sect3.value}
</opt:section>
</opt:section>
</opt:section>
</opt:root>