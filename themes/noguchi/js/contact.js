'use strict';
import React from 'react';
import ReactDOM from 'react-dom';
const axios = require('axios');
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

const selector = pawtucketUIApps.contact.selector;
const appData = pawtucketUIApps.contact.data;
const sectionName = appData.sectionName;

	class ContactFormMessage extends React.Component {
		render() {
			return (
			    (this.props.message) ? <div className='alert alert-danger'>{this.props.message}</div> : null
			);
		}
	}
	class ContactForm extends React.Component {
		constructor(props) {
			super(props);
			this.state = {
				statusMessage: '',
				values: this.initializeValues(),
				errors: this.initializeValues(),
				settings: {
					sectionName: sectionName,
					...props
				}
			}
			this.handleForm = this.handleForm.bind(this);
			this.sendMessage = this.sendMessage.bind(this);
		}

		initializeValues() {
			return {
				name: '',
				email: '',
				idno: '',
				institution: '',
				description: ''
			};
		}

		handleForm(e) {
			let n = e.target.name;
			let v = e.target.value;

			let state = this.state;
			state.values[n] = v;
			this.setState(state);
		}

		sendMessage(e) {
			let state = this.state;
			let that = this;
			state.statusMessage = "Sending...";
			this.setState(state);
			let formData = new FormData();
			for(let k in this.state.values) {
				formData.append(k, this.state.values[k]);
			}
			formData.append('g-recaptcha-response', document.getElementById('g-recaptcha-response').value);
			axios.post("/index.php/Contact/Send",  formData)
				.then(function (resp) {
					let data = resp.data;

					if (data.status !== 'ok') {
						// error
						state.statusMessage = data.error;
						state.errors = that.initializeValues();
						if(data.fieldErrors) {
							for(let k in data.fieldErrors) {
								if((state.errors[k] !== undefined)) {
									state.errors[k] = data.fieldErrors[k];
								}
							}
						}
						state.errors['captcha'] = data.fieldErrors['captcha'];
						grecaptcha.reset();
					} else {
						// success
						state.statusMessage = "Sent message";
						state.values = that.initializeValues();	// Clear form elements
						state.errors = that.initializeValues();	// Clear form errors
						grecaptcha.reset(); // reload recaptcha
					}
					setTimeout(function() {
						state.statusMessage = '';
						that.setState(state);
					}, 3000);

					that.setState(state);

				})
				.catch(function (error) {
					console.log("Error while attempting to send message: ", error);
				});

			e.preventDefault();
		}

		render() {
			return (
			    <div>
					<ContactFormMessage message={this.state.statusMessage} />
					<form className='ca-form'>
						<ul className='form'>
							<li>{(this.state.errors.name) ? <div className='alert alert-danger'>{this.state.errors.name}</div> : null}<input name='name' value={this.state.values.name} onChange={this.handleForm} type='text' placeholder='Name*' required /></li>
							<li>{(this.state.errors.email) ? <div className='alert alert-danger'>{this.state.errors.email}</div> : null}<input name='email' value={this.state.values.email} onChange={this.handleForm} type='email' placeholder='Email*' required /></li>
							<li>{(this.state.errors.institution) ? <div className='alert alert-danger'>{this.state.errors.institution}</div> : null}<input name='institution' value={this.state.values.institution} onChange={this.handleForm} type='text' placeholder='Institution' /></li>
							<li>{(this.state.errors.idno) ? <div className='alert alert-danger'>{this.state.errors.idno}</div> : null}<input name='idno' value={this.state.values.idno} onChange={this.handleForm} type='text' placeholder='Object Identifier' /></li>
							<li>{(this.state.errors.description) ? <div className='alert alert-danger'>{this.state.errors.description}</div> : null}<textarea name='description' value={this.state.values.description} onChange={this.handleForm} placeholder='Description*' required /></li>
							<li>{(this.state.errors.captcha) ? <div className='alert alert-danger'>{this.state.errors.captcha}</div> : null}
								<div id="reCaptcha"></div>
								<input type='submit' className='button' value='Submit' onClick={this.sendMessage} />
							</li>
						</ul>
					</form>
				</div>
			);
		}
	}

/**
 * Initialize browse and render into DOM. This function is exported to allow the Pawtucket
 * app loaders to insert this application into the current view.
 */
export default function _init() {
	ReactDOM.render(<ContactForm />, document.querySelector(selector));
}
