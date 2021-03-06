void abhishektest::module_load_simulate_simulate_2()
{
   delete_widgets_layouts( panel1_sub_widgets, panel1_sub_layouts );
   panel1_widget_map.clear();
   panel1_inputs.clear();
   panel1_outputs.clear();
   panel1_map_input.clear();
   panel1_is_input.clear();

   current_module_id = "simulate_2";

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

   {
      QLabel * lbl = new QLabel( "Input file  ", this );
      lbl->setMaximumHeight( 22 );
      lbl->show();
      gl->addWidget( lbl, ppos, 0 );
      panel1_sub_widgets.push_back( lbl );

      mQPushButton * pb = new mQPushButton( this );
      pb->setText( "Browse..." );
      pb->setMaximumHeight( 22 );
      pb->show();
      gl->addWidget( pb, ppos, 1 );
      panel1_sub_widgets.push_back( pb );
      connect( pb, SIGNAL( clicked() ), this, SLOT( browse_simulate_2_input1() ) );

      QLabel * lbl2 = new QLabel( "", this );
      pb->mbuddy = lbl2;
      lbl2->setMaximumHeight( 22 );
      lbl2->show();
      gl->addWidget( lbl2, ppos, 2 );
      panel1_sub_widgets.push_back( lbl2 );

      QString id = "simulate_2:input1";
      global_data_types[ id ] = "file";
      panel1_widget_map[ id ] = pb;
      panel1_inputs.push_back( id );
      save_default_value( id );

      ppos++;
   }
   QHBoxLayout * hbl = new QHBoxLayout( 0 );
   {
      mQPushButton * pb = new mQPushButton( this );
      pb->setText( "Submit" );
      pb->setMaximumHeight( 22 );
      pb->show();
      connect( pb, SIGNAL( clicked() ), SLOT( module_submit_simulate_simulate_2() ) );
      hbl->addWidget( pb );
      hbl->addSpacing( 3 );
      panel1_sub_widgets.push_back( pb );
   }
   {
      mQPushButton * pb = new mQPushButton( this );
      pb->setText( "Reset to default values" );
      pb->setMaximumHeight( 22 );
      pb->show();
      connect( pb, SIGNAL( clicked() ), SLOT(module_reset_simulate_simulate_2() ) );
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

      QString id = "simulate_2:_errorMessages";
      global_data_types[ id ] = "lbl";
      panel1_widget_map[ id ] = lbl;
      panel1_inputs.push_back( id );
      save_default_value( id );
   }
   ppos++;
   {
      QLabel * lbl = new QLabel( "Output value 1  ", this );
      lbl->setMaximumHeight( 22 );
      lbl->show();
      gl->addWidget( lbl, ppos, 0 );
      panel1_sub_widgets.push_back( lbl );

      QLineEdit * le = new QLineEdit( this );
      le->setMaximumHeight( 22 );
//      le->setPalette( *palette_le );
      le->setReadOnly( true );
      le->show();
      gl->addWidget( le, ppos, 1 );
      panel1_sub_widgets.push_back( le );

      QString id = "simulate_2:output1";
      global_data_types[ id ] = "le";
      panel1_widget_map[ id ] = le;
      panel1_outputs.push_back( id );
      save_default_value( id );

      ppos++;
   }
   {
      QLabel * lbl = new QLabel( "output file  ", this );
      lbl->setMaximumHeight( 22 );
      lbl->show();
      gl->addWidget( lbl, ppos, 0 );
      panel1_sub_widgets.push_back( lbl );

      mQLabel * lbl2 = new mQLabel( "", this );
      
      lbl2->setMaximumHeight( 22 );
      lbl2->show();
      gl->addWidget( lbl2, ppos, 1 );
      panel1_sub_widgets.push_back( lbl2 );
      connect( lbl2, SIGNAL( pressed() ), this, SLOT( browse_simulate_2_output2() ) );

      QString id = "simulate_2:output2";
      global_data_types[ id ] = "outfile";
      panel1_widget_map[ id ] = lbl2;
      panel1_outputs.push_back( id );
      save_default_value( id );

      ppos++;
   }
   {
      QLabel * lbl = new QLabel( "output file 2  ", this );
      lbl->setMaximumHeight( 22 );
      lbl->show();
      gl->addWidget( lbl, ppos, 0 );
      panel1_sub_widgets.push_back( lbl );

      mQLabel * lbl2 = new mQLabel( "", this );
      
      lbl2->setMaximumHeight( 22 );
      lbl2->show();
      gl->addWidget( lbl2, ppos, 1 );
      panel1_sub_widgets.push_back( lbl2 );
      connect( lbl2, SIGNAL( pressed() ), this, SLOT( browse_simulate_2_output3() ) );

      QString id = "simulate_2:output3";
      global_data_types[ id ] = "outfile";
      panel1_widget_map[ id ] = lbl2;
      panel1_outputs.push_back( id );
      save_default_value( id );

      ppos++;
   }
   {
      QLabel * lbl = new QLabel( "output files  ", this );
      lbl->setMaximumHeight( 22 );
      lbl->show();
      gl->addWidget( lbl, ppos, 0 );
      panel1_sub_widgets.push_back( lbl );

      mQLabel * lbl2 = new mQLabel( "", this );
      
      lbl2->setMaximumHeight( 22 );
      lbl2->show();
      gl->addWidget( lbl2, ppos, 1 );
      panel1_sub_widgets.push_back( lbl2 );
      connect( lbl2, SIGNAL( pressed() ), this, SLOT( browse_simulate_2_output4() ) );

      QString id = "simulate_2:output4";
      global_data_types[ id ] = "outfiles";
      panel1_widget_map[ id ] = lbl2;
      panel1_outputs.push_back( id );
      save_default_value( id );

      ppos++;
   }

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

void abhishektest::module_reset_simulate_simulate_2()
{
   reset_default_values();
}

void abhishektest::module_submit_simulate_simulate_2()
{
   save_last_values();
   reset_output_values( "default_value" );
   // qDebug() << input_to_json( "simulate_2" );
   // we should check if process already running

   QString program = "/home/abhishek/Desktop/GenApp/abhishektest/bin/simulate_2";

   QFileInfo qfi( program );
   if ( !qfi.exists() || !qfi.isExecutable() || qfi.isDir() )
   {
      QString id = "simulate_2";
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
      QString id = "simulate_2";
      QString key = id + ":_errorMessages";    
      global_data[ key + ":last_value" ] = QVariant( "Unexpected results:\n   error => could not create temporary directory for submission" );
      if ( current_module_id == id &&
           global_data_types.count( key ) )
      {
         reset_value( key, "last_value" );
      }
   }        

   process_simulate_simulate_2 = new QProcess( this );
   process_simulate_simulate_2->setWorkingDirectory( dir );

   QStringList args;
   args << input_to_json( "simulate_2", dir );

   connect( process_simulate_simulate_2, 
            SIGNAL( error( QProcess::ProcessError ) ), 
            this, 
            SLOT( error_simulate_simulate_2( QProcess::ProcessError ) ) );
   connect( process_simulate_simulate_2, 
            SIGNAL( readyReadStandardOutput() ), 
            this, 
            SLOT( readyReadStandardOutput_simulate_simulate_2() ) );
   connect( process_simulate_simulate_2, 
            SIGNAL( readyReadStandardOutput() ), 
            this, 
            SLOT( readyReadStandardError_simulate_simulate_2() ) );
   connect( process_simulate_simulate_2, 
            SIGNAL( finished( int, QProcess::ExitStatus ) ), 
            this, 
            SLOT( finished_simulate_simulate_2( int, QProcess::ExitStatus ) ) );
   process_simulate_simulate_2->start( program, args );
   process_json[ "simulate_2" ] = "";
   qDebug() << "process started";
}

void abhishektest::readyReadStandardOutput_simulate_simulate_2()
{
   process_json[ "simulate_2" ] += QString( process_simulate_simulate_2->readAllStandardOutput() );
}

void abhishektest::readyReadStandardError_simulate_simulate_2()
{
   qDebug() << process_simulate_simulate_2->readAllStandardError();
}

void abhishektest::error_simulate_simulate_2( QProcess::ProcessError e )
{
   qDebug() << "process_simulate_simulate_2->error()" << e;
}

void abhishektest::finished_simulate_simulate_2( int, QProcess::ExitStatus )
{
   qDebug() << "process_simulate_simulate_2->finished()";
   readyReadStandardOutput_simulate_simulate_2();
   readyReadStandardError_simulate_simulate_2();

   disconnect( process_simulate_simulate_2, 0, 0, 0 );
   // post process data into output fields, add unexpected data etc
   // delete process_simulate_simulate_2;
   // process_simulate_simulate_2 = (QProcess *) 0;
   process_results( "simulate_2" );
}
