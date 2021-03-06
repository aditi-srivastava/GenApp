void abhishektest::module_load_build_build_2()
{
   delete_widgets_layouts( panel1_sub_widgets, panel1_sub_layouts );
   panel1_widget_map.clear();
   panel1_inputs.clear();
   panel1_outputs.clear();
   panel1_map_input.clear();
   panel1_is_input.clear();

   current_module_id = "build_2";

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
      QLabel * lbl = new QLabel( "Input value 1  ", this );
      lbl->setMaximumHeight( 22 );
      lbl->show();
      gl->addWidget( lbl, ppos, 0 );
      panel1_sub_widgets.push_back( lbl );

      QLineEdit * le = new QLineEdit( this );
      le->setMaximumHeight( 22 );
//      le->setPalette( *palette_le );
      le->setValidator( new QDoubleValidator( le ) );
      ((QDoubleValidator*)le->validator())->setDecimals( 8 );
      le->setText( QString( "%1" ).arg( 0 ) ); 
      ((QDoubleValidator*)le->validator())->setBottom( 0 ); 
      ((QDoubleValidator*)le->validator())->setTop( 1e+50 ); 
      le->show();
      gl->addWidget( le, ppos, 1 );
      panel1_sub_widgets.push_back( le );

      QString id = "build_2:input1";
      global_data_types[ id ] = "le";
      panel1_widget_map[ id ] = le;
      panel1_inputs.push_back( id );
      save_default_value( id );

      ppos++;
   }
   {
      QLabel * lbl = new QLabel( "Input value 2  ", this );
      lbl->setMaximumHeight( 22 );
      lbl->show();
      gl->addWidget( lbl, ppos, 0 );
      panel1_sub_widgets.push_back( lbl );

      QLineEdit * le = new QLineEdit( this );
      le->setMaximumHeight( 22 );
//      le->setPalette( *palette_le );
      le->setValidator( new QIntValidator( le ) );
      le->setText( QString( "%1" ).arg( 1 ) ); 
      ((QIntValidator*)le->validator())->setBottom( -10 ); 
      ((QIntValidator*)le->validator())->setTop( 10 ); 
      le->show();
      gl->addWidget( le, ppos, 1 );
      panel1_sub_widgets.push_back( le );

      QString id = "build_2:input2";
      global_data_types[ id ] = "le";
      panel1_widget_map[ id ] = le;
      panel1_inputs.push_back( id );
      save_default_value( id );

      ppos++;
   }
   {
      mQLabel * lbl = new mQLabel( "Input value 3  ", this );
      lbl->setMaximumHeight( 22 );
      lbl->show();
      gl->addWidget( lbl, ppos, 0 );
      panel1_sub_widgets.push_back( lbl );

      QCheckBox * cb = new QCheckBox( this );
      cb->setMaximumHeight( 22 );
//      cb->setPalette( *palette_cb );
      cb->setChecked( true ); 
      cb->show();
      gl->addWidget( cb, ppos, 1 );
      panel1_sub_widgets.push_back( cb );
      connect( lbl, SIGNAL( pressed() ), cb, SLOT( toggle() ) );

      QString id = "build_2:input3";
      global_data_types[ id ] = "cb";
      panel1_widget_map[ id ] = cb;
      panel1_inputs.push_back( id );
      save_default_value( id );

      ppos++;
   }
   {
      mQLabel * lbl = new mQLabel( "Radiobutton a  ", this );
      lbl->setMaximumHeight( 22 );
      lbl->show();
      gl->addWidget( lbl, ppos, 0 );
      panel1_sub_widgets.push_back( lbl );

      mQRadioButton * rb = new mQRadioButton( this );
      rb->setMaximumHeight( 22 );
//      rb->setPalette( *palette_cb );
     
      rb->show();
      gl->addWidget( rb, ppos, 1 );
      rb->data = QVariant( "input4a" );
      panel1_sub_widgets.push_back( rb );
      connect( lbl, SIGNAL( pressed() ), rb, SLOT( toggle() ) );

      if ( !panel1_widget_map.count( "gb:input4" ) )
      {
         panel1_widget_map[ "gb:input4" ] = new QGroupBox( 0 );
         ((QGroupBox *)panel1_widget_map[ "gb:input4" ])->setFlat( true );
      }

// #warn radio needs work
//      ((QGroupBox *)panel1_widget_map[ "hb:input4" ])->insert( rb );

      QString id = "build_2:input4a";
      global_data_types[ id ] = "rb";
      panel1_widget_map[ id ] = rb;
      panel1_inputs.push_back( id );
      save_default_value( id );
      panel1_map_input[ id ] = "build_2:input4";

      ppos++;
   }
   {
      mQLabel * lbl = new mQLabel( "Radiobutton b  ", this );
      lbl->setMaximumHeight( 22 );
      lbl->show();
      gl->addWidget( lbl, ppos, 0 );
      panel1_sub_widgets.push_back( lbl );

      mQRadioButton * rb = new mQRadioButton( this );
      rb->setMaximumHeight( 22 );
//      rb->setPalette( *palette_cb );
     
      rb->show();
      gl->addWidget( rb, ppos, 1 );
      rb->data = QVariant( "input4b" );
      panel1_sub_widgets.push_back( rb );
      connect( lbl, SIGNAL( pressed() ), rb, SLOT( toggle() ) );

      if ( !panel1_widget_map.count( "gb:input4" ) )
      {
         panel1_widget_map[ "gb:input4" ] = new QGroupBox( 0 );
         ((QGroupBox *)panel1_widget_map[ "gb:input4" ])->setFlat( true );
      }

// #warn radio needs work
//      ((QGroupBox *)panel1_widget_map[ "hb:input4" ])->insert( rb );

      QString id = "build_2:input4b";
      global_data_types[ id ] = "rb";
      panel1_widget_map[ id ] = rb;
      panel1_inputs.push_back( id );
      save_default_value( id );
      panel1_map_input[ id ] = "build_2:input4";

      ppos++;
   }
   {
      mQLabel * lbl = new mQLabel( "Radiobutton c  ", this );
      lbl->setMaximumHeight( 22 );
      lbl->show();
      gl->addWidget( lbl, ppos, 0 );
      panel1_sub_widgets.push_back( lbl );

      mQRadioButton * rb = new mQRadioButton( this );
      rb->setMaximumHeight( 22 );
//      rb->setPalette( *palette_cb );
      rb->setChecked( true ); 
      rb->show();
      gl->addWidget( rb, ppos, 1 );
      rb->data = QVariant( "input4c" );
      panel1_sub_widgets.push_back( rb );
      connect( lbl, SIGNAL( pressed() ), rb, SLOT( toggle() ) );

      if ( !panel1_widget_map.count( "gb:input4" ) )
      {
         panel1_widget_map[ "gb:input4" ] = new QGroupBox( 0 );
         ((QGroupBox *)panel1_widget_map[ "gb:input4" ])->setFlat( true );
      }

// #warn radio needs work
//      ((QGroupBox *)panel1_widget_map[ "hb:input4" ])->insert( rb );

      QString id = "build_2:input4c";
      global_data_types[ id ] = "rb";
      panel1_widget_map[ id ] = rb;
      panel1_inputs.push_back( id );
      save_default_value( id );
      panel1_map_input[ id ] = "build_2:input4";

      ppos++;
   }
   {
      QLabel * lbl = new QLabel( "1st listbox  ", this );
      // lbl->setMaximumHeight( 22 );
      lbl->show();
      gl->addWidget( lbl, ppos, 0 );
      panel1_sub_widgets.push_back( lbl );

      QListWidget * lw = new QListWidget( this );
      //  lb->setMaximumHeight( 22 );
//      lw->setPalette( *palette_le );
      lw->show();
      gl->addWidget( lw, ppos, 1 );
      panel1_sub_widgets.push_back( lw );

      QString id = "build_2:input5";
      { QString qs = "choice 1"; lw->addItem( qs ); panel1_map_input[ QString( "lw:%1" ).arg( qs ) ] = "c1"; }{ QString qs = "choice 2"; lw->addItem( qs ); panel1_map_input[ QString( "lw:%1" ).arg( qs ) ] = "c2"; }{ QString qs = "choice 3"; lw->addItem( qs ); panel1_map_input[ QString( "lw:%1" ).arg( qs ) ] = "c3"; }
      lw->setCurrentRow( 0 );
      global_data_types[ id ] = "lw";
      panel1_widget_map[ id ] = lw;
      panel1_inputs.push_back( id );
      save_default_value( id );
      panel1_map_input[ id ] = "build_2:input5";

      ppos++;
   }
   {
      QLabel * lbl = new QLabel( "textarea  ", this );
      // lbl->setMaximumHeight( 22 );
      lbl->show();
      gl->addWidget( lbl, ppos, 0 );
      panel1_sub_widgets.push_back( lbl );

      QTextEdit * te = new QTextEdit( this );
      //  lb->setMaximumHeight( 22 );
//      te->setPalette( *palette_le );
      te->setText( "this is a text areamultiline" ); 
      te->show();
      gl->addWidget( te, ppos, 1 );
      panel1_sub_widgets.push_back( te );

      QString id = "build_2:input6";
      global_data_types[ id ] = "te";
      panel1_widget_map[ id ] = te;
      panel1_inputs.push_back( id );
      save_default_value( id );
      // panel1_map_input[ id ] = "build_2:input6";

      ppos++;
   }
   QHBoxLayout * hbl = new QHBoxLayout( 0 );
   {
      mQPushButton * pb = new mQPushButton( this );
      pb->setText( "Submit" );
      pb->setMaximumHeight( 22 );
      pb->show();
      connect( pb, SIGNAL( clicked() ), SLOT( module_submit_build_build_2() ) );
      hbl->addWidget( pb );
      hbl->addSpacing( 3 );
      panel1_sub_widgets.push_back( pb );
   }
   {
      mQPushButton * pb = new mQPushButton( this );
      pb->setText( "Reset to default values" );
      pb->setMaximumHeight( 22 );
      pb->show();
      connect( pb, SIGNAL( clicked() ), SLOT(module_reset_build_build_2() ) );
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

      QString id = "build_2:_errorMessages";
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

      QString id = "build_2:output1";
      global_data_types[ id ] = "le";
      panel1_widget_map[ id ] = le;
      panel1_outputs.push_back( id );
      save_default_value( id );

      ppos++;
   }
   {
      QLabel * lbl = new QLabel( "Radio output 2a  ", this );
      lbl->setMaximumHeight( 22 );
      lbl->show();
      gl->addWidget( lbl, ppos, 0 );
      panel1_sub_widgets.push_back( lbl );

      mQRadioButton * rb = new mQRadioButton( this );
      rb->setMaximumHeight( 22 );
//      rb->setPalette( *palette_cb );
      rb->setEnabled( false );
      rb->show();
      gl->addWidget( rb, ppos, 1 );
      rb->data = QVariant( "output2a" );
      panel1_sub_widgets.push_back( rb );

      if ( !panel1_widget_map.count( "gb:output2" ) )
      {
         panel1_widget_map[ "gb:output2" ] = new QGroupBox( 0 );
      }
      
// #warn radio needs work
//      ((QButtonGroup *)panel1_widget_map[ "bg:output2" ])->insert( rb );

      QString id = "build_2:output2a";
      global_data_types[ id ] = "rb";
      panel1_widget_map[ id ] = rb;
      panel1_outputs.push_back( id );
      save_default_value( id );
      //      panel1_map_input[ id ] = "build_2:output2";

      ppos++;
   }
   {
      QLabel * lbl = new QLabel( "Radio output 2b  ", this );
      lbl->setMaximumHeight( 22 );
      lbl->show();
      gl->addWidget( lbl, ppos, 0 );
      panel1_sub_widgets.push_back( lbl );

      mQRadioButton * rb = new mQRadioButton( this );
      rb->setMaximumHeight( 22 );
//      rb->setPalette( *palette_cb );
      rb->setEnabled( false );
      rb->show();
      gl->addWidget( rb, ppos, 1 );
      rb->data = QVariant( "output2b" );
      panel1_sub_widgets.push_back( rb );

      if ( !panel1_widget_map.count( "gb:output2" ) )
      {
         panel1_widget_map[ "gb:output2" ] = new QGroupBox( 0 );
      }
      
// #warn radio needs work
//      ((QButtonGroup *)panel1_widget_map[ "bg:output2" ])->insert( rb );

      QString id = "build_2:output2b";
      global_data_types[ id ] = "rb";
      panel1_widget_map[ id ] = rb;
      panel1_outputs.push_back( id );
      save_default_value( id );
      //      panel1_map_input[ id ] = "build_2:output2";

      ppos++;
   }
   {
      QLabel * lbl = new QLabel( "Radio output 2c  ", this );
      lbl->setMaximumHeight( 22 );
      lbl->show();
      gl->addWidget( lbl, ppos, 0 );
      panel1_sub_widgets.push_back( lbl );

      mQRadioButton * rb = new mQRadioButton( this );
      rb->setMaximumHeight( 22 );
//      rb->setPalette( *palette_cb );
      rb->setEnabled( false );
      rb->show();
      gl->addWidget( rb, ppos, 1 );
      rb->data = QVariant( "output2c" );
      panel1_sub_widgets.push_back( rb );

      if ( !panel1_widget_map.count( "gb:output2" ) )
      {
         panel1_widget_map[ "gb:output2" ] = new QGroupBox( 0 );
      }
      
// #warn radio needs work
//      ((QButtonGroup *)panel1_widget_map[ "bg:output2" ])->insert( rb );

      QString id = "build_2:output2c";
      global_data_types[ id ] = "rb";
      panel1_widget_map[ id ] = rb;
      panel1_outputs.push_back( id );
      save_default_value( id );
      //      panel1_map_input[ id ] = "build_2:output2";

      ppos++;
   }
   {
      QLabel * lbl = new QLabel( "checkbox out 3  ", this );
      lbl->setMaximumHeight( 22 );
      lbl->show();
      gl->addWidget( lbl, ppos, 0 );
      panel1_sub_widgets.push_back( lbl );

      QCheckBox * cb = new QCheckBox( this );
      cb->setMaximumHeight( 22 );
//      cb->setPalette( *palette_cb );
      cb->setEnabled( false );
      cb->show();
      gl->addWidget( cb, ppos, 1 );
      panel1_sub_widgets.push_back( cb );

      QString id = "build_2:output3";
      global_data_types[ id ] = "cb";
      panel1_widget_map[ id ] = cb;
      panel1_outputs.push_back( id );
      save_default_value( id );

      ppos++;
   }
   {
      QLabel * lbl = new QLabel( "checkbox out 4  ", this );
      lbl->setMaximumHeight( 22 );
      lbl->show();
      gl->addWidget( lbl, ppos, 0 );
      panel1_sub_widgets.push_back( lbl );

      QCheckBox * cb = new QCheckBox( this );
      cb->setMaximumHeight( 22 );
//      cb->setPalette( *palette_cb );
      cb->setEnabled( false );
      cb->show();
      gl->addWidget( cb, ppos, 1 );
      panel1_sub_widgets.push_back( cb );

      QString id = "build_2:output4";
      global_data_types[ id ] = "cb";
      panel1_widget_map[ id ] = cb;
      panel1_outputs.push_back( id );
      save_default_value( id );

      ppos++;
   }
   {
      QLabel * lbl = new QLabel( "output textarea  ", this );
      // lbl->setMaximumHeight( 22 );
      lbl->show();
      gl->addWidget( lbl, ppos, 0 );
      panel1_sub_widgets.push_back( lbl );

      QTextEdit * te = new QTextEdit( this );
      //  lb->setMaximumHeight( 22 );
//      te->setPalette( *palette_le );
      te->show();
      te->setReadOnly( true );
      gl->addWidget( te, ppos, 1 );
      panel1_sub_widgets.push_back( te );

      QString id = "build_2:output5";
      global_data_types[ id ] = "te";
      panel1_widget_map[ id ] = te;
      panel1_outputs.push_back( id );
      save_default_value( id );
      // panel1_map_input[ id ] = "build_2:output5";

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

void abhishektest::module_reset_build_build_2()
{
   reset_default_values();
}

void abhishektest::module_submit_build_build_2()
{
   save_last_values();
   reset_output_values( "default_value" );
   // qDebug() << input_to_json( "build_2" );
   // we should check if process already running

   QString program = "/home/abhishek/Desktop/GenApp/abhishektest/bin/build_2";

   QFileInfo qfi( program );
   if ( !qfi.exists() || !qfi.isExecutable() || qfi.isDir() )
   {
      QString id = "build_2";
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
      QString id = "build_2";
      QString key = id + ":_errorMessages";    
      global_data[ key + ":last_value" ] = QVariant( "Unexpected results:\n   error => could not create temporary directory for submission" );
      if ( current_module_id == id &&
           global_data_types.count( key ) )
      {
         reset_value( key, "last_value" );
      }
   }        

   process_build_build_2 = new QProcess( this );
   process_build_build_2->setWorkingDirectory( dir );

   QStringList args;
   args << input_to_json( "build_2", dir );

   connect( process_build_build_2, 
            SIGNAL( error( QProcess::ProcessError ) ), 
            this, 
            SLOT( error_build_build_2( QProcess::ProcessError ) ) );
   connect( process_build_build_2, 
            SIGNAL( readyReadStandardOutput() ), 
            this, 
            SLOT( readyReadStandardOutput_build_build_2() ) );
   connect( process_build_build_2, 
            SIGNAL( readyReadStandardOutput() ), 
            this, 
            SLOT( readyReadStandardError_build_build_2() ) );
   connect( process_build_build_2, 
            SIGNAL( finished( int, QProcess::ExitStatus ) ), 
            this, 
            SLOT( finished_build_build_2( int, QProcess::ExitStatus ) ) );
   process_build_build_2->start( program, args );
   process_json[ "build_2" ] = "";
   qDebug() << "process started";
}

void abhishektest::readyReadStandardOutput_build_build_2()
{
   process_json[ "build_2" ] += QString( process_build_build_2->readAllStandardOutput() );
}

void abhishektest::readyReadStandardError_build_build_2()
{
   qDebug() << process_build_build_2->readAllStandardError();
}

void abhishektest::error_build_build_2( QProcess::ProcessError e )
{
   qDebug() << "process_build_build_2->error()" << e;
}

void abhishektest::finished_build_build_2( int, QProcess::ExitStatus )
{
   qDebug() << "process_build_build_2->finished()";
   readyReadStandardOutput_build_build_2();
   readyReadStandardError_build_build_2();

   disconnect( process_build_build_2, 0, 0, 0 );
   // post process data into output fields, add unexpected data etc
   // delete process_build_build_2;
   // process_build_build_2 = (QProcess *) 0;
   process_results( "build_2" );
}
