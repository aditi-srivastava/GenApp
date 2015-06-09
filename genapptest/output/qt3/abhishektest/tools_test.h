void abhishektest::module_load_tools_test()
{
   delete_widgets_layouts( panel1_sub_widgets, panel1_sub_layouts );
   panel1_widget_map.clear();
   panel1_inputs.clear();
   panel1_outputs.clear();
   panel1_map_input.clear();
   panel1_is_input.clear();

   current_module_id = "test";

   {
      QLabel * lbl = new QLabel( "", this );
      lbl->setMaximumHeight( 12 );
      lbl->show();
      gl_panel1->addWidget( lbl, 1, 0 );
      panel1_sub_widgets.push_back( lbl );

   }

   QGridLayout * gl = new QGridLayout( 0, 1, 1, 3, 3 );
   {
      QLabel * lbl = new QLabel( "", this );
      lbl->setMaximumHeight( 12 );
      lbl->show();
      gl->addWidget( lbl, 0, 2 );
      panel1_sub_widgets.push_back( lbl );
   }

   int ppos = 0; // the base position in the gl_sub_panel

   QHBoxLayout * hbl = new QHBoxLayout( 0 );
   {
      mQPushButton * pb = new mQPushButton( this );
      pb->setText( "Submit" );
      pb->setMaximumHeight( 22 );
      pb->show();
      connect( pb, SIGNAL( clicked() ), SLOT( module_submit_tools_test() ) );
      hbl->addWidget( pb );
      hbl->addSpacing( 3 );
      panel1_sub_widgets.push_back( pb );
   }
   {
      mQPushButton * pb = new mQPushButton( this );
      pb->setText( "Reset to default values" );
      pb->setMaximumHeight( 22 );
      pb->show();
      connect( pb, SIGNAL( clicked() ), SLOT(module_reset_tools_test() ) );
      hbl->addWidget( pb );
      panel1_sub_widgets.push_back( pb );
   }
   gl->addMultiCellLayout( hbl, ppos, ppos, 0, 1 );
   panel1_sub_layouts.push_back( hbl );
   ppos++;

   {
      QLabel * lbl = new QLabel( "", this );
      lbl->setPalette( *palette_lbl_error );
      lbl->show();
      gl->addMultiCellWidget( lbl, ppos, ppos, 0, 1 );
      panel1_sub_widgets.push_back( lbl );

      QString id = "test:_errorMessages";
      global_data_types[ id ] = "lbl";
      panel1_widget_map[ id ] = lbl;
      panel1_inputs.push_back( id );
      save_default_value( id );
   }
   ppos++;

   reset_last_values();

   gl->setColStretch( 0, 0 );
   gl->setColStretch( 1, 0 );
   gl->setColStretch( 2, 1 );
   gl_panel1->addMultiCellLayout( gl, 2, 2, 0, 2 );
   panel1_sub_layouts.push_back( gl );
   for ( int i = 0; i < (int) panel1_inputs.size(); ++i )
   {
      panel1_is_input[ panel1_inputs[ i ] ] = true;
   }
}

void abhishektest::module_reset_tools_test()
{
   reset_default_values();
}

void abhishektest::module_submit_tools_test()
{
   save_last_values();
   reset_output_values( "default_value" );
   // qDebug( input_to_json( "test" ) );
   // we should check if process already running

   QString program = "/home/abhishek/Desktop/GenApp/abhishektest/bin/test";

   QFileInfo qfi( program );
   if ( !qfi.exists() || !qfi.isExecutable() || qfi.isDir() )
   {
      QString id = "test";
      QString key = id + ":_errorMessages";    
      global_data[ key + ":last_value" ] = QVariant( "Unexpected results:\n   error => command not found or not executable" );
      if ( current_module_id == id &&
           global_data_types.count( key ) )
      {
         reset_value( key, "last_value" );
      }
      return;
   }
   //   qDebug( "process exists and isExecutable" );
   
   // make a temporary directory:

   QString dir = UTILITY::unique_directory( "/tmp/abhishektest" );
   if ( dir.isEmpty() )
   {  
      QString id = "test";
      QString key = id + ":_errorMessages";    
      global_data[ key + ":last_value" ] = QVariant( "Unexpected results:\n   error => could not create temporary directory for submission" );
      if ( current_module_id == id &&
           global_data_types.count( key ) )
      {
         reset_value( key, "last_value" );
      }
   }        

   process_tools_test = new QProcess( this );
   process_tools_test->setWorkingDirectory( dir );
   process_tools_test->addArgument( program );

   process_tools_test->addArgument( input_to_json( "test", dir ) );
   connect( process_tools_test, 
            SIGNAL( readyReadStdout() ), 
            this, 
            SLOT( readyReadStdout_tools_test() ) );
   connect( process_tools_test, 
            SIGNAL( readyReadStderr() ), 
            this, 
            SLOT( readyReadStderr_tools_test() ) );
   connect( process_tools_test, 
            SIGNAL( launchFinished() ), 
            this, 
            SLOT( launchFinished_tools_test() ) );
   connect( process_tools_test, 
            SIGNAL( processExited() ), 
            this, 
            SLOT( processExited_tools_test() ) );
   if ( !process_tools_test->start() )
   { 
      qDebug( "error starting process" );
      return;
   }
   process_json[ "test" ] = "";
   qDebug( "process started" );
}

void abhishektest::readyReadStdout_tools_test()
{
   while ( process_tools_test->canReadLineStdout() )
   {
      process_json[ "test" ] += process_tools_test->readLineStdout();
   }
}

void abhishektest::readyReadStderr_tools_test()
{
   while ( process_tools_test->canReadLineStderr() )
   {
      qDebug( process_tools_test->readLineStderr() );
   }
}

void abhishektest::launchFinished_tools_test()
{
   // qDebug( "process_tools_test->launchFinished()" );
   disconnect( process_tools_test,
               SIGNAL( launchFinished_tools_test() ),
               0, 0 );
}

void abhishektest::processExited_tools_test()
{
   qDebug( "process_tools_test->processExited()" );
   readyReadStdout_tools_test();
   readyReadStderr_tools_test();

   disconnect( process_tools_test, 0, 0, 0 );
   // post process data into output fields, add unexpected data etc
   // delete process_tools_test;
   // process_tools_test = (QProcess *) 0;
   process_results( "test" );
}
