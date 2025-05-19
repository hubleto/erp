import ModalSimple from 'adios/ModalSimple';
import React, { Component } from 'react';

export interface FormActivitySelectorProps {
  calendarConfigs: any,
  clickConfig: any,
  onCallback: Function,
}

export interface FormActivitySelectorState {
  formSelected?: JSX.Element;
}

export default class FormActivitySelector<P, S> extends Component<FormActivitySelectorProps, FormActivitySelectorState>{

  props: FormActivitySelectorProps;
  state: FormActivitySelectorState;

  translationContext: string = 'HubletoApp\\Community\\Calendar\\Loader::Components\\FormActivitySelector';

  render(): JSX.Element {
    return (
      <>
        <div className='modal-header'>
          <div className="modal-header-left"></div>
          <div className="modal-header-title">New event</div>
          <div className="modal-header-right">
            <button className="btn btn-close" onClick={() => this.props.onCallback()}>
              <span className="text !py-2">&times;</span>
            </button>
          </div>
        </div>
        <div className="badge m-4 px-4 text-2xl">
          {this.props.clickConfig?.date}
          &nbsp;
          {this.props.clickConfig?.time}
        </div>
        <div className="badge badge-info m-4 px-4 text-xl">
          Choose calendar to which the event should be created.
        </div>
        <div className='flex gap-2 flex-col px-4 mt-4'>
          {this.props.calendarConfigs.map((item, index) => {
            if (item.addNewActivityButtonText) {
              return <>
                <button
                  key={index}
                  className='btn btn-transparent btn-large'
                  style={{borderLeft: '3em solid ' + item.color}}
                  onClick={() => {
                    this.setState({formSelected: globalThis.main.renderReactElement(item.formComponent,
                      {
                        description: {
                          defaultValues: {
                            date_start: this.props.clickConfig?.date,
                            time_start: this.props.clickConfig?.time
                          }
                        },
                        id: -1,
                        showInModal: true,
                        showInModalSimple: true,
                        onClose:() => {this.setState({formSelected: null}), this.props.onCallback()},
                        onSaveCallback:() => {this.setState({formSelected: null}), this.props.onCallback()},
                      })
                    });
                  }}
                >
                  <span className='text text-center self-center !h-auto text-lg'>{item.addNewActivityButtonText}</span>
                </button>
              </>
            } else {
              return null;
            }
          })}
        </div>
        {this.state?.formSelected ?
          <ModalSimple
            uid='activity_form'
            isOpen={true}
            type='inside-parent'
          >
            {this.state.formSelected}
          </ModalSimple>
        : <></>}
      </>
    );
  }
}
