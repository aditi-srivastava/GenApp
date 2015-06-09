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

   QGridLayout * gl = new QGridLayout( 0 ); //, 1, 1, 3, 3 );
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
   gl->addLayout( hbl, ppos, 0, 1, 2 );
   panel1_sub_layouts.push_back( hbl );
   ppos++;
   {
      QLabel * lbl = new QLabel( "", this );
//      lbl->setPalette( *palette_lbl_error );
      lbl->show();
      gl->addWidget( lbl, ppos, 0, 1, 2 );
      panel1_sub_widgets.push_back( lbl );

      QString id = "test:_errorMessages";
      global_data_types[ id ] = "lbl";
      panel1_widget_map[ id ] = lbl;
      panel1_inputs.push_back( id );
      save_default_value( id );
   }
   ppos++;

   reset_last_values();

   gl->setColumnStretch( 0, 0 );
   gl->setColumnStretch( 1, 0 );
   gl->setColumnStretch( 2, 1 );
   gl_panel1->addLayout( gl, 2, 0, 1, 3 );
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
   // qDebug() << input_to_json( "test" );
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
   //   qDebug() << "process exists and isExecutable";
   
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

   QStringList args;
   args << input_to_json( "test", dir );

   connect( process_tools_test, 
            SIGNAL( error( QProcess::ProcessError ) ), 
            this, 
            SLOT( error_tools_test( QProcess::ProcessError ) ) );
   connect( process_tools_test, 
            SIGNAL( readyReadStandardOutput() ), 
            this, 
            SLOT( readyReadStandardOutput_tools_test() ) );
   connect( process_tools_test, 
            SIGNAL( readyReadStandardOutput() ), 
            this, 
            SLOT( readyReadStandardError_tools_test() ) );
   connect( process_tools_test, 
            SIGNAL( finished( int, QProcess::ExitStatus ) ), 
            this, 
            SLOT( finished_tools_test( int, QProcess::ExitStatus ) ) );
   process_tools_test->start( program, args );
   process_json[ "test" ] = "";
   qDebug() << "process started";
}

void abhishektest::readyReadStandardOutput_tools_test()
{
   process_json[ "test" ] += QString( process_tools_test->readAllStandardOutput() );
}

void abhishektest::readyReadStandardError_tools_test()
{
   qDebug() << process_tools_test->readAllStandardError();
}

void abhishektest::error_tools_test( QProcess::ProcessError e )
{
   qDebug() << "process_tools_test->error()" << e;
}

void abhishektest::finished_tools_test( int, QProcess::ExitStatus )
{
   qDebug() << "process_tools_test->finished()";
   readyReadStandardOutput_tools_test();
   readyReadStandardError_tools_test();

   disconnect( process_tools_test, 0, 0, 0 );
   // post process data into output fields, add unexpected data etc
   // delete process_tools_test;
   // process_tools_test = (QProcess *) 0;
   process_results( "test" );
}
