void abhishektest::module_load_tools_filetest()
{
   delete_widgets_layouts( panel1_sub_widgets, panel1_sub_layouts );
   panel1_widget_map.clear();
   panel1_inputs.clear();
   panel1_outputs.clear();
   panel1_map_input.clear();
   panel1_is_input.clear();

   current_module_id = "filetest";

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
      mQLabel * lbl = new mQLabel( "How about yet another file  ", this );
      lbl->setMaximumHeight( 22 );
      lbl->show();
      gl->addWidget( lbl, ppos, 0 );
      panel1_sub_widgets.push_back( lbl );

      QCheckBox * cb = new QCheckBox( this );
      cb->setMaximumHeight( 22 );
//      cb->setPalette( *palette_cb );
     
      cb->show();
      gl->addWidget( cb, ppos, 1 );
      panel1_sub_widgets.push_back( cb );
      connect( lbl, SIGNAL( pressed() ), cb, SLOT( toggle() ) );

      QString id = "filetest:cbfile3cb";
      global_data_types[ id ] = "cb";
      panel1_widget_map[ id ] = cb;
      panel1_inputs.push_back( id );
      save_default_value( id );

      ppos++;
   }
   {
      QLabel * lbl = new QLabel( "How about a bunch of files  ", this );
      lbl->setMaximumHeight( 22 );
      lbl->show();
      gl->addWidget( lbl, ppos, 0 );
      panel1_sub_widgets.push_back( lbl );

      QLineEdit * le = new QLineEdit( this );
      le->setMaximumHeight( 22 );
//      le->setPalette( *palette_le );
      le->setValidator( new QIntValidator( le ) );
      le->setText( QString( "%1" ).arg( 0 ) ); 
      ((QIntValidator*)le->validator())->setBottom( 0 ); 
      ((QIntValidator*)le->validator())->setTop( 5 ); 
      le->show();
      gl->addWidget( le, ppos, 1 );
      panel1_sub_widgets.push_back( le );

      QString id = "filetest:file4i";
      global_data_types[ id ] = "le";
      panel1_widget_map[ id ] = le;
      panel1_inputs.push_back( id );
      save_default_value( id );

      ppos++;
   }
   {
      mQLabel * lbl = new mQLabel( "How about yet another path  ", this );
      lbl->setMaximumHeight( 22 );
      lbl->show();
      gl->addWidget( lbl, ppos, 0 );
      panel1_sub_widgets.push_back( lbl );

      QCheckBox * cb = new QCheckBox( this );
      cb->setMaximumHeight( 22 );
//      cb->setPalette( *palette_cb );
     
      cb->show();
      gl->addWidget( cb, ppos, 1 );
      panel1_sub_widgets.push_back( cb );
      connect( lbl, SIGNAL( pressed() ), cb, SLOT( toggle() ) );

      QString id = "filetest:cbpath3cb";
      global_data_types[ id ] = "cb";
      panel1_widget_map[ id ] = cb;
      panel1_inputs.push_back( id );
      save_default_value( id );

      ppos++;
   }
   {
      QLabel * lbl = new QLabel( "How about a bunch of paths  ", this );
      lbl->setMaximumHeight( 22 );
      lbl->show();
      gl->addWidget( lbl, ppos, 0 );
      panel1_sub_widgets.push_back( lbl );

      QLineEdit * le = new QLineEdit( this );
      le->setMaximumHeight( 22 );
//      le->setPalette( *palette_le );
      le->setValidator( new QIntValidator( le ) );
      le->setText( QString( "%1" ).arg( 0 ) ); 
      ((QIntValidator*)le->validator())->setBottom( 0 ); 
      ((QIntValidator*)le->validator())->setTop( 5 ); 
      le->show();
      gl->addWidget( le, ppos, 1 );
      panel1_sub_widgets.push_back( le );

      QString id = "filetest:path4i";
      global_data_types[ id ] = "le";
      panel1_widget_map[ id ] = le;
      panel1_inputs.push_back( id );
      save_default_value( id );

      ppos++;
   }
   {
      mQLabel * lbl = new mQLabel( "How about yet another remote file  ", this );
      lbl->setMaximumHeight( 22 );
      lbl->show();
      gl->addWidget( lbl, ppos, 0 );
      panel1_sub_widgets.push_back( lbl );

      QCheckBox * cb = new QCheckBox( this );
      cb->setMaximumHeight( 22 );
//      cb->setPalette( *palette_cb );
     
      cb->show();
      gl->addWidget( cb, ppos, 1 );
      panel1_sub_widgets.push_back( cb );
      connect( lbl, SIGNAL( pressed() ), cb, SLOT( toggle() ) );

      QString id = "filetest:cbfile3cb";
      global_data_types[ id ] = "cb";
      panel1_widget_map[ id ] = cb;
      panel1_inputs.push_back( id );
      save_default_value( id );

      ppos++;
   }
   {
      QLabel * lbl = new QLabel( "How about a bunch of remote files  ", this );
      lbl->setMaximumHeight( 22 );
      lbl->show();
      gl->addWidget( lbl, ppos, 0 );
      panel1_sub_widgets.push_back( lbl );

      QLineEdit * le = new QLineEdit( this );
      le->setMaximumHeight( 22 );
//      le->setPalette( *palette_le );
      le->setValidator( new QIntValidator( le ) );
      le->setText( QString( "%1" ).arg( 0 ) ); 
      ((QIntValidator*)le->validator())->setBottom( 0 ); 
      ((QIntValidator*)le->validator())->setTop( 5 ); 
      le->show();
      gl->addWidget( le, ppos, 1 );
      panel1_sub_widgets.push_back( le );

      QString id = "filetest:rfile4i";
      global_data_types[ id ] = "le";
      panel1_widget_map[ id ] = le;
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
      connect( pb, SIGNAL( clicked() ), SLOT( module_submit_tools_filetest() ) );
      hbl->addWidget( pb );
      hbl->addSpacing( 3 );
      panel1_sub_widgets.push_back( pb );
   }
   {
      mQPushButton * pb = new mQPushButton( this );
      pb->setText( "Reset to default values" );
      pb->setMaximumHeight( 22 );
      pb->show();
      connect( pb, SIGNAL( clicked() ), SLOT(module_reset_tools_filetest() ) );
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

      QString id = "filetest:_errorMessages";
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

void abhishektest::module_reset_tools_filetest()
{
   reset_default_values();
}

void abhishektest::module_submit_tools_filetest()
{
   save_last_values();
   reset_output_values( "default_value" );
   // qDebug() << input_to_json( "filetest" );
   // we should check if process already running

   QString program = "/home/abhishek/Desktop/GenApp/abhishektest/bin/filetest";

   QFileInfo qfi( program );
   if ( !qfi.exists() || !qfi.isExecutable() || qfi.isDir() )
   {
      QString id = "filetest";
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
      QString id = "filetest";
      QString key = id + ":_errorMessages";    
      global_data[ key + ":last_value" ] = QVariant( "Unexpected results:\n   error => could not create temporary directory for submission" );
      if ( current_module_id == id &&
           global_data_types.count( key ) )
      {
         reset_value( key, "last_value" );
      }
   }        

   process_tools_filetest = new QProcess( this );
   process_tools_filetest->setWorkingDirectory( dir );

   QStringList args;
   args << input_to_json( "filetest", dir );

   connect( process_tools_filetest, 
            SIGNAL( error( QProcess::ProcessError ) ), 
            this, 
            SLOT( error_tools_filetest( QProcess::ProcessError ) ) );
   connect( process_tools_filetest, 
            SIGNAL( readyReadStandardOutput() ), 
            this, 
            SLOT( readyReadStandardOutput_tools_filetest() ) );
   connect( process_tools_filetest, 
            SIGNAL( readyReadStandardOutput() ), 
            this, 
            SLOT( readyReadStandardError_tools_filetest() ) );
   connect( process_tools_filetest, 
            SIGNAL( finished( int, QProcess::ExitStatus ) ), 
            this, 
            SLOT( finished_tools_filetest( int, QProcess::ExitStatus ) ) );
   process_tools_filetest->start( program, args );
   process_json[ "filetest" ] = "";
   qDebug() << "process started";
}

void abhishektest::readyReadStandardOutput_tools_filetest()
{
   process_json[ "filetest" ] += QString( process_tools_filetest->readAllStandardOutput() );
}

void abhishektest::readyReadStandardError_tools_filetest()
{
   qDebug() << process_tools_filetest->readAllStandardError();
}

void abhishektest::error_tools_filetest( QProcess::ProcessError e )
{
   qDebug() << "process_tools_filetest->error()" << e;
}

void abhishektest::finished_tools_filetest( int, QProcess::ExitStatus )
{
   qDebug() << "process_tools_filetest->finished()";
   readyReadStandardOutput_tools_filetest();
   readyReadStandardError_tools_filetest();

   disconnect( process_tools_filetest, 0, 0, 0 );
   // post process data into output fields, add unexpected data etc
   // delete process_tools_filetest;
   // process_tools_filetest = (QProcess *) 0;
   process_results( "filetest" );
}
